<?php

namespace Dew\Cli\Database;

use Dew\Cli\Contracts\DatabaseInstanceQuoter;
use Dew\Cli\Contracts\InstanceQuotation;
use Dew\Cli\Contracts\ServerlessInstanceQuotation;
use Dew\Cli\InteractsWithDew;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Style\StyleInterface;

class CreateDatabaseInstanceHandler
{
    use InteractsWithDew;

    /**
     * The project ID.
     */
    public int $projectId;

    /**
     * The database instance builder.
     */
    protected CreateDatabaseInstance $builder;

    /**
     * The database instance quoter.
     */
    protected DatabaseInstanceQuoter $quoter;

    /**
     * The database instance type.
     */
    public string $type;

    public function __construct(
        protected InputInterface $input,
        protected StyleInterface $io
    ) {
        //
    }

    /**
     * Configure project ID.
     */
    public function forProject(int $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Handle database instance creation.
     */
    public function handle(): int
    {
        $this->io->title('Create a new database instance');

        $this->configureType()
            ->configureName()
            ->configureEngine()
            ->configureEngineVersion()
            ->configureDeployment()
            ->configureStorageType()
            ->configureZone()
            ->configureClass()
            ->configureStorage()
            ->configureFeatures()
            ->showQuotation();

        if ($this->io->confirm('Confirm to create the database instance')) {
            $this->builder()->create();

            $this->io->success('The database instance created successfully!');
        }

        return Command::SUCCESS;
    }

    /**
     * Ask for database instance name if necessarily.
     */
    protected function askName(): string
    {
        return $this->input->getArgument('name') ?:
            $this->io->ask('What is your database instance name');
    }

    /**
     * Configure database instance name.
     */
    protected function configureName(): self
    {
        $this->builder()->name($this->askName());

        return $this;
    }

    /**
     * Ask for database instance type if necessarily.
     */
    protected function askType(): string
    {
        return $this->input->getArgument('type') ?:
            $this->io->choice('What kind of instance do you want to setup',
                $types = InstanceType::all(), $types[0],
            );
    }

    /**
     * Configure database instance type.
     */
    protected function configureType(): self
    {
        $this->type = $this->askType();

        return $this;
    }

    /**
     * Ask for database instance engine if necessarily.
     */
    protected function askEngine(): string
    {
        return $this->input->getArgument('engine') ?:
            $this->io->choice('What database engine do you want to setup',
                $engines = Engine::all(), $engines[0]
            );
    }

    /**
     * Configure database instance engine.
     */
    protected function configureEngine(): self
    {
        $engine = $this->askEngine();

        $this->builder()->engine($engine);
        $this->quoter()->engine($engine);

        return $this;
    }

    /**
     * Ask for database engine version.
     */
    protected function askEngineVersion(): string
    {
        return $this->io->choice('How about the database engine version',
            $versions = $this->quoter()->availableEngineVersions(), $versions[0]
        );
    }

    /**
     * Configure database engine version.
     */
    protected function configureEngineVersion(): self
    {
        $engineVersion = $this->askEngineVersion();

        $this->builder()->engineVersion($engineVersion);
        $this->quoter()->engineVersion($engineVersion);

        return $this;
    }

    /**
     * Ask for database deployment option.
     */
    protected function askDeployment(): string
    {
        return $this->io->choice('What deployment option do you want to choose',
            $options = $this->quoter()->availableDeploymentOptions(), $options[0]
        );
    }

    /**
     * Configure database deployment option.
     */
    protected function configureDeployment(): self
    {
        $option = $this->askDeployment();

        $this->builder()->deployment($option);
        $this->quoter()->deployment($option);

        return $this;
    }

    /**
     * Ask for storage type.
     */
    protected function askStorageType(): string
    {
        return $this->io->choice('What storage type do you want to choose',
            $types = $this->quoter()->availableStorageTypes(), $types[0]
        );
    }

    /**
     * Configure storage type.
     */
    protected function configureStorageType(): self
    {
        $type = $this->askStorageType();

        $this->builder()->storageType($type);
        $this->quoter()->storageType($type);

        return $this;
    }

    /**
     * Ask for deployment zone.
     */
    protected function askZone(): string
    {
        return $this->io->choice('Which zone the database instance is deployed to',
            $zones = $this->quoter()->availableZones(), $zones[0]
        );
    }

    /**
     * Configure deployment zone.
     */
    protected function configureZone(): self
    {
        $zone = $this->askZone();

        $this->builder()->zone($zone);
        $this->quoter()->zone($zone);

        return $this;
    }

    /**
     * Ask for instance class.
     */
    protected function askClass(): string
    {
        return $this->io->choice('What instance class do you want to setup',
            $classes = $this->quoter()->availableClasses(), $classes[0]
        );
    }

    /**
     * Configure instance class.
     */
    protected function configureClass(): self
    {
        $class = $this->askClass();

        $this->builder()->class($class);
        $this->quoter()->class($class);

        return $this;
    }

    /**
     * Ask for storage size.
     */
    protected function askStorage(): string
    {
        $range = $this->quoter()->availableStorageRange($this->quoter()->class);

        return $this->io->ask('How much storage in GB do you want to setup',
            (string) $range->min()
        );
    }

    /**
     * Configure storage size.
     */
    protected function configureStorage(): self
    {
        $storage = $this->askStorage();

        $this->builder()->storage($storage);
        $this->quoter()->storage($storage);

        return $this;
    }

    /**
     * Configure specific features.
     */
    protected function configureFeatures(): self
    {
        $method = sprintf('configure%sInstance', Str::studly($this->type));

        if (method_exists($this, $method)) {
            $this->{$method}();
        }

        return $this;
    }

    /**
     * Configure subscription database instance.
     */
    protected function configureSubscriptionInstance(): void
    {
        $this->configureSubscriptionTerm();
    }

    /**
     * Configure instance subscription term.
     */
    protected function configureSubscriptionTerm(): self
    {
        $type = $this->askSubscriptionType();
        $term = $this->askSubscriptionTerm($type);

        $this->builder()->subscribeFor($term, $type);
        $this->quoter()->subscribeFor($term, $type);

        return $this;
    }

    /**
     * Ask for instance subscription type.
     */
    protected function askSubscriptionType(): string
    {
        return $this->io->choice('What subscription type is feel right to you',
            $types = SubscriptionType::all(), $types[0]
        );
    }

    /**
     * Ask for instance subscription term.
     */
    protected function askSubscriptionTerm(string $type): int
    {
        return (int) $this->io->ask(
            sprintf('How many %s do you want to subscribe the instance', Str::plural($type)),
            '1'
        );
    }

    /**
     * Configure serverless database instance.
     */
    protected function configureServerlessInstance(): void
    {
        $this->configureServerlessScale()
            ->configureServerlessFeatures();
    }

    /**
     * Configure serverless database instance scaling range.
     */
    protected function configureServerlessScale(): self
    {
        [$min, $max] = [$this->askServerlessScaleMin(), $this->askServerlessScaleMax()];

        $this->builder()->scales($min, $max);
        $this->quoter()->scales($min, $max);

        return $this;
    }

    /**
     * Configure serverless database instance features.
     */
    protected function configureServerlessFeatures(): self
    {
        $this->builder()->autoPause($this->askServerlessAutoPause());
        $this->builder()->forceScale($this->askServerlessScalingPolicy());

       return $this;
    }

    /**
     * Ask for whether to enable instance auto-pause feature.
     */
    protected function askServerlessAutoPause(): bool
    {
        return $this->io->confirm('Enable auto-pause feature', false);
    }

    /**
     * Ask for whether to enable instance force scaling.
     */
    protected function askServerlessScalingPolicy(): bool
    {
        return $this->io->confirm('Enable force scaling', false);
    }

    /**
     * Ask for the minimum RCU for instance scaling down.
     */
    protected function askServerlessScaleMin(): float|int
    {
        return +$this->io->ask('What is the minimum RCU for instance scaling down', '0.5');
    }

    /**
     * Ask for the maximum RCU for instance scaling up.
     */
    protected function askServerlessScaleMax(): int
    {
        return +$this->io->ask('What is the maximum RCU for instance scaling up', '8');
    }

    /**
     * Show instance quotation for the current setup.
     */
    protected function showQuotation(): self
    {
        $quotation = $this->quoter()->getQuotation();

        return $this->showQuotationSection($quotation)
            ->showPromotionSection($quotation)
            ->showCouponSection($quotation);
    }

    /**
     * Show instance quotation section.
     */
    protected function showQuotationSection(InstanceQuotation $quotation): self
    {
        $method = 'showQuotationSectionFor'.Str::studly($this->builder()->type()).'Instance';

        if (method_exists($this, $method)) {
            return $this->{$method}($quotation);
        }

        return $this->showGeneralQuotationSection($quotation);
    }

    /**
     * Show the general quotation section.
     */
    protected function showGeneralQuotationSection(InstanceQuotation $quotation): self
    {
        $this->io->table(
            ['Currency', 'Original', 'Discount', 'Trade'],
            [[
                $quotation->getCurrency(),
                $quotation->getOriginalPrice(),
                $quotation->getDiscount(),
                $quotation->getTradePrice(),
            ]],
        );

        $this->io->text('Billed hourly.');

        return $this;
    }

    /**
     * Show the quotation section for subscription instance.
     */
    protected function showQuotationSectionForSubscriptionInstance(InstanceQuotation $quotation): self
    {
        $this->io->table(
            ['Currency', 'Original', 'Discount', 'Trade'],
            [[
                $quotation->getCurrency(),
                $quotation->getOriginalPrice(),
                $quotation->getDiscount(),
                $quotation->getTradePrice(),
            ]],
        );

        $this->io->text(sprintf('Subscription term: %d %s',
            $term = $this->quoter()->subscriptionTerm,
            Str::plural($this->quoter()->subscriptionType, $term)
        ));

        return $this;
    }

    /**
     * Show the quotation section for serverless instance.
     */
    protected function showQuotationSectionForServerlessInstance(ServerlessInstanceQuotation $quotation): self
    {
        $this->io->table(
            ['Currency', 'From', 'To'],
            [[
                $quotation->getCurrency(),
                $quotation->getMinRcuPrice(),
                $quotation->getMaxRcuPrice(),
            ]],
        );

        $this->io->text('Billed based on RCU, hourly');

        return $this;
    }

    /**
     * Show the promotion section.
     */
    protected function showPromotionSection(InstanceQuotation $quotation): self
    {
        $promotion = $quotation->getPromotion();

        if (count($promotion) === 0) {
            return $this;
        }

        $this->io->table(
            ['ID', 'Promotion'],
            array_map(fn ($rule) => [$rule->id(), $rule->name()], $promotion),
        );

        return $this;
    }

    /**
     * Show the coupon section.
     */
    protected function showCouponSection(InstanceQuotation $quotation): self
    {
        $coupons = $quotation->getCoupons();

        if (count($coupons) === 0) {
            return $this;
        }

        $this->io->table(
            ['Selected', 'No.', 'Coupon', 'Description'],
            array_map(fn ($coupon) => [
                $coupon->isSelected(),
                $coupon->number(),
                $coupon->name(),
                $coupon->description(),
            ], $coupons),
        );

        return $this;
    }

    /**
     * Resolve database instance builder by the given instance type.
     */
    protected function resolveBuilder(string $type): CreateDatabaseInstance
    {
        return match ($type) {
            InstanceType::PAY_AS_YOU_GO => new CreatePayAsYouGoDatabaseInstance,
            InstanceType::SUBSCRIPTION => new CreateSubscriptionDatabaseInstance,
            InstanceType::SERVERLESS => new CreateServerlessDatabaseInstance,
            default => throw new InvalidArgumentException('Unsupported database instance type.'),
        };
    }

    /**
     * Resolves database instance quoter.
     */
    protected function quoter(): DatabaseInstanceQuoter
    {
        return $this->quoter ??= $this->resolveQuoter($this->type)
            ->tokenUsing($this->token)
            ->forProject($this->projectId);
    }

    /**
     * Resolve database instance quoter by given instance type.
     */
    protected function resolveQuoter(string $type): QuoteDatabaseInstance
    {
        return match ($type) {
            InstanceType::PAY_AS_YOU_GO => new QuotePayAsYouGoDatabaseInstance,
            InstanceType::SUBSCRIPTION => new QuoteSubscriptionDatabaseInstance,
            InstanceType::SERVERLESS => new QuoteServerlessDatabaseInstance,
            default => throw new InvalidArgumentException('Unsupported database instance type.'),
        };
    }

    /**
     * Configure database instance quoter.
     */
    public function quoterUsing(DatabaseInstanceQuoter $quoter): self
    {
        $this->quoter = $quoter;

        return $this;
    }

    /**
     * Resolves database instance builder.
     */
    protected function builder(): CreateDatabaseInstance
    {
        return $this->builder ??= $this->resolveBuilder($this->type)
            ->tokenUsing($this->token)
            ->forProject($this->projectId);
    }
}
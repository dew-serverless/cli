<?php

namespace Dew\Cli\Database;

class QuoteServerlessDatabaseInstance extends QuoteDatabaseInstance
{
    use ManagesServerlessScales;

    /**
     * Get the database instance type.
     */
    public function type(): string
    {
        return InstanceType::SERVERLESS;
    }

    /**
     * Make a new instance quotation by given response.
     *
     * @param  array<string, mixed>  $response
     */
    protected function newInstanceQuotation(array $response): InstanceQuotation
    {
        $price = $response['PriceInfo'];

        $quotation = new ServerlessInstanceQuotation(
            $price['Currency'], $price['OriginalPrice'], $price['DiscountPrice'], $price['TradePrice']
        );

        return $quotation
            ->setMinRcuPrice($response['TradeMinRCUAmount'])
            ->setMaxRcuPrice($response['TradeMaxRCUAmount']);
    }

    /**
     * Get the quotation request payload.
     *
     * @return array<string, mixed>
     */
    protected function toQuotationRequest(): array
    {
        return array_merge(parent::toQuotationRequest(), [
            'scales_min' => $this->scaleMin,
            'scales_max' => $this->scaleMax,
        ]);
    }
}

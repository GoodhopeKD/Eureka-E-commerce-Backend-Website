<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class VisitViewFollowCountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $today                    = 0;
        $past_seven_days          = 0;
        $past_one_month           = 0;
        $past_three_months        = 0;
        $past_six_months          = 0;
        $past_one_year            = 0;
        $total                    = 0;

        foreach ($this->resource as $item) {
            $datetime = Carbon::parse($item->created_at);

            if ($datetime->isToday()) $today++;
            if ($datetime->gt(new Carbon('-7 days'))) $past_seven_days++;
            if ($datetime->gt(new Carbon('-1 month'))) $past_one_month++;
            if ($datetime->gt(new Carbon('-3 months'))) $past_three_months++;
            if ($datetime->gt(new Carbon('-6 months'))) $past_six_months++;
            if ($datetime->gt(new Carbon('-1 year'))) $past_one_year++;
            $total++;
        }

        return [
            'today'                     => $today,
            'past_seven_days'           => $past_seven_days,
            'past_one_month'            => $past_one_month,
            'past_three_months'         => $past_three_months,
            'past_six_months'           => $past_six_months,
            'past_one_year'             => $past_one_year,
            'total'                     => $total,
        ];
    }
}

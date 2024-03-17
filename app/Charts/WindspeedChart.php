<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class WindspeedChart
{
    protected $chart;

    public function __construct(LarapexChart $chart)
    {
        $this->chart = $chart;
    }

    public function build(): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->chart->lineChart()
            ->setTitle('Windspeed')
            // ->setSubtitle('Physical sales vs Digital sales.')
            ->addData('Windspeed', [70, 29, 77, 28, 55, 45])
            ->setXAxis(['January', 'February', 'March', 'April', 'May', 'June']);
    }
}

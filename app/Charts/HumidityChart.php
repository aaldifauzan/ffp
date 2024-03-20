<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class WindspeedChart
{
    protected $chart2;

    public function __construct(LarapexChart $chart2)
    {
        $this->chart2 = $chart2;
    }

    public function build($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->chart2->lineChart()
            ->setTitle('Humidity')
            ->setLabels($labels)
            ->addData('Humidity', $data);
    }
}
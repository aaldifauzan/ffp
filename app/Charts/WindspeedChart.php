<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class WindspeedChart
{
    protected $chart1;

    public function __construct(LarapexChart $chart1)
    {
        $this->chart1 = $chart1;
    }

    public function build($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->chart1->lineChart()
            ->setTitle('Windspeed')
            ->setLabels($labels)
            ->addData('Windspeed', $data);
    }
}
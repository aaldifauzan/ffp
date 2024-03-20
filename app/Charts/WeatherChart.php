<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class WeatherChart
{
    protected $windspeedChart;
    protected $humidityChart;

    public function __construct(LarapexChart $chart1, LarapexChart $chart2)
    {
        $this->windspeedChart = $chart1;
        $this->humidityChart = $chart2;
    }

    public function buildWindspeedChart($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->windspeedChart->lineChart()
            ->setTitle('Windspeed')
            ->setLabels($labels)
            ->addData('Windspeed', $data);
    }

    public function buildHumidityChart($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->humidityChart->lineChart()
            ->setTitle('Humidity')
            ->setLabels($labels)
            ->addData('Humidity', $data);
    }
}

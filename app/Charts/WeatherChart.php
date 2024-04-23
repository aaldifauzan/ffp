<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class WeatherChart
{
    protected $windspeedChart;
    protected $humidityChart;
    protected $rainfallChart;
    protected $temperatureChart; // Adding property for temperature chart

    public function __construct(LarapexChart $chart1, LarapexChart $chart2, LarapexChart $chart3, LarapexChart $chart4) // Adding parameter for temperature chart
    {
        $this->windspeedChart = $chart1;
        $this->humidityChart = $chart2;
        $this->rainfallChart = $chart3;
        $this->temperatureChart = $chart4; // Initializing temperature chart property
    }

    public function buildWindspeedChart($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        $roundedData = array_map(function ($value) {
            return round($value, 2); // Rounding to two decimal places
        }, $data);
        return $this->windspeedChart->lineChart()
            ->setTitle('Windspeed')
            ->setLabels($labels)
            ->addData('Windspeed', $roundedData)
            ->setStroke(2);
    }
    
    public function buildHumidityChart($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        $roundedData = array_map(function ($value) {
            return round($value, 2); // Rounding to two decimal places
        }, $data);
        return $this->humidityChart->lineChart()
            ->setTitle('Humidity')
            ->setLabels($labels)
            ->addData('Humidity', $roundedData)
            ->setStroke(2);
    }
    
    public function buildRainfallChart($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        $roundedData = array_map(function ($value) {
            return round($value, 2); // Rounding to two decimal places
        }, $data);
        return $this->rainfallChart->lineChart()
            ->setTitle('Rainfall')
            ->setLabels($labels)
            ->addData('Rainfall', $roundedData)
            ->setStroke(2);
    }
    
    public function buildTemperatureChart($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        $roundedData = array_map(function ($value) {
            return round($value, 4); // Rounding to two decimal places
        }, $data);
        return $this->temperatureChart->lineChart()
            ->setTitle('Temperature')
            ->setLabels($labels)
            ->addData('Temperature', $roundedData)
            ->setStroke(2);
    }
}
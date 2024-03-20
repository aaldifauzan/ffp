<?php

namespace App\Charts;

use ArielMejiaDev\LarapexCharts\LarapexChart;

class WeatherChart
{
    protected $windspeedChart;
    protected $humidityChart;
    protected $rainfallChart;
    protected $temperatureChart; // Menambahkan properti untuk grafik suhu

    public function __construct(LarapexChart $chart1, LarapexChart $chart2, LarapexChart $chart3, LarapexChart $chart4) // Menambahkan parameter untuk grafik suhu
    {
        $this->windspeedChart = $chart1;
        $this->humidityChart = $chart2;
        $this->rainfallChart = $chart3;
        $this->temperatureChart = $chart4; // Menginisialisasi properti grafik suhu
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

    public function buildRainfallChart($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->rainfallChart->lineChart()
            ->setTitle('Rainfall')
            ->setLabels($labels)
            ->addData('Rainfall', $data);
    }

    public function buildTemperatureChart($data, $labels): \ArielMejiaDev\LarapexCharts\LineChart
    {
        return $this->temperatureChart->lineChart()
            ->setTitle('Temperature')
            ->setLabels($labels)
            ->addData('Temperature', $data);
    }
}

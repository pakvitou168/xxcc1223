<?php
namespace App\Imports;
use App\Models\Insurance\Auto\AutoTemp;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\Make\MakeModel;

class VehiclesImport implements ToModel, WithHeadingRow, WithMultipleSheets
{
    private $productCode;
    private $vehicleUsageOptions;
    private $makeList;
    private $productSpecification;
    private $master_data_type;
    private $master_data_id;
    private $negotiationRate;

    public function __construct($productCode, $vehicleUsageOptions, $makeList, $productSpecification, $master_data_type, $master_data_id, $negotiationRate)
    {
        $this->productCode = $productCode;
        $this->vehicleUsageOptions = $vehicleUsageOptions;
        $this->makeList = $makeList;
        $this->productSpecification = $productSpecification;
        $this->master_data_type = $master_data_type;
        $this->master_data_id = $master_data_id;
        $this->negotiationRate = $negotiationRate;
    }

    public function model(array $row)
    {
        if($this->vehicleUsageOptions->contains('name', $row['vehicle_usage'])){
            $make = $this->makeList->whereIn('label', $row['make'])->values();
            if($make->count() > 0){
                $model = MakeModel::select('id')
                    ->where('make_id', $make[0]['value'])
                    ->where('model', $row['model'])
                    ->where('status', 'ACT')
                    ->first();
                if($model){
                    return new AutoTemp([
                        'product_code' => $this->productCode,
                        'master_data_type' => $this->master_data_type,
                        'master_data_id' => $this->master_data_id,
                        'model_id' => $model->id,
                        'plate_no' => $row['plate_no'],
                        'chassis_no' => $row['chassis_no'],
                        'engine_no' => $row['engine_no'],
                        'manufacturing_year' => $row['manufacturing_year'],
                        'cubic' => $row['cubic_capacity'],
                        'vehicle_value' => $row['value_of_vehicle'],
                        'passenger' => $this->productSpecification === 'PASSENGER' ? $row['passenger_or_tonnage'] : null,
                        'tonnage' => $this->productSpecification === 'TONNAGE' ? $row['passenger_or_tonnage'] : null,
                        'surcharge' => $row['surcharge'],
                        'discount' => $row['discount'],
                        'ncd' => $row['ncd'],
                        'negotiation_rate' => $this->negotiationRate,
                        'selected_cover_pkg' => preg_replace('/\s+/', '', $row['covers']),
                        'commercial_vehicle_type' => $this->productSpecification,
                        'vehicle_usage' => $row['vehicle_usage'],
                        'remark' => $row['remark']
                    ]);
                }
            }
        }
    }

    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
}

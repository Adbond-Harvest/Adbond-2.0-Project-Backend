<?php

namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use PDF;

use App\Models\Package;
use App\Models\PackageSize;
use App\Models\PackagePhoto;

use App\Enums\ProjectFilter;

use App\Exports\PackageExport;

class PackageService
{
    public $count = false;
    public $projectId = null;

    public function save($data)
    {
        $package = $this->getByName($data['name']);
        if(!$package) {
            $package = new Package;
            $package->name = $data['name'];
            $package->project_id = $data['projectId'];
            $package->user_id = $data['userId'];

            $package->state_id = $data['stateId'];
            if(isset( $data['address'])) $package->address = $data['address'];
            $package->size = $data['size'];
            $package->amount = $data['amount'];
            $package->units = $data['units'];
            $package->available_units = $data['units'];
            if(isset( $data['discount'])) $package->discount = $data['discount'];
            if(isset( $data['minPrice'])) $package->min_price = $data['minPrice'];
            if(isset( $data['installmentDuration'])) $package->installment_duration = $data['installmentDuration'];
            if(isset( $data['infrastructureFee'])) $package->infrastructure_fee = $data['infrastructureFee'];

            if(isset($data['description'])) $package->description = $data['description'];
            if(isset($data['benefits'])) $package->benefits = $data['benefits'];
            if(isset($data['brochureFileId'])) $package->brochure_file_id = $data['brochureFileId'];
            if(isset($data['installmentOption'])) $package->installment_option = $data['installmentOption'];
            if(isset($data['vrUrl'])) $package->vr_url = $data['vrUrl'];
            $package->save();
            return $package;
        }
        return null;
    }

    public function savePhotos($fileIds, $package)
    {
        foreach($fileIds as $fileId) {
            $packagePhoto = PackagePhoto::where("package_id", $package->id)->where("photo_id", $fileId)->first();
            if(!$packagePhoto) {
                $packagePhoto = new PackagePhoto;
                $packagePhoto->package_id = $package->id;
                $packagePhoto->photo_id = $fileId;
                $packagePhoto->save();
            }
        }
    }

    public function update($data, $package)
    {
        if(isset($data['name'])) $package->name = $data['name'];
        if(isset( $data['projectId'])) $package->project_id = $data['projectId'];
        if(isset( $data['stateId'])) $package->state_id = $data['stateId'];
        if(isset( $data['address'])) $package->address = $data['address'];
        if(isset( $data['size'])) $package->size = $data['size'];
        if(isset( $data['amount'])) $package->amount = $data['amount'];
        if(isset( $data['units'])) $package->units = $data['units'];
        if(isset( $data['availableUnits'])) $package->available_units = $data['availableUnits'];
        if(isset( $data['discount'])) $package->discount = $data['discount'];
        if(isset( $data['minPrice'])) $package->min_price = $data['minPrice'];
        if(isset( $data['installmentDuration'])) $package->installment_duration = $data['installmentDuration'];
        if(isset( $data['infrastructureFee'])) $package->infrastructure_fee = $data['infrastructureFee'];

        if(isset($data['description'])) $package->description = $data['description'];
        if(isset($data['benefits'])) $package->benefits = $data['benefits'];
        if(isset($data['brochureFileId'])) $package->brochure_file_id = $data['brochureFileId'];
        if(isset($data['installmentOption'])) $package->installment_option = $data['installmentOption'];
        if(isset($data['vrUrl'])) $package->vr_url = $data['vrUrl'];  
        $package->update();

        return $package;
    }

    public function markAsSoldOut($package)
    {
        $package->sold_out = true;
        $package->update();
        return $package;
    }

    public function markAsBackInStock($package)
    {
        $package->sold_out = false;
        $package->update();
        return $package;
    }

    public function activate($project)
    {
        $project->active = true;
        $project->update();
        return $project;
    }

    public function deactivate($project)
    {
        $project->active = false;
        $project->deactivated_at = now();
        $project->update();
        return $project;
    }

    public function delete($project)
    {
        if($project->canDelete()) {
            $project->delete();
        }
    }

    // public function removePhotos($fileIds, $package)
    // {
    //     // $packagePhotos = $package->packagePhotos()->whereIn('photo_id', $fileIds)->get();
    //     foreach ($packagePhotos as $packagePhoto) $packagePhoto->delete(); 

    // }

    public function packages($projectId, $with=[], $offset=0, $perPage=null)
    {
        $query = Package::with($with)->where("project_id", $projectId);
        if($this->count) return $query->count();

        if($perPage==null) $perPage=env('PAGINATION_PER_PAGE');
        return $query->offset($offset)->limit($perPage)->get();
    }

    public function package($id, $with=[])
    {
        return Package::with($with)->where("id", $id)->first();
    }

    public function getByName($name, $with=[])
    {
        return Package::with($with)->where("name", $name)->first();
    }

    public function getPackagePhotoIds($package)
    {
        return PackagePhoto::where("package_id", $package->id)->pluck("photo_id")->toArray();
    }

    public function filter($filter, $with=[], $offset=0, $perPage=null)
    {
        $query = Package::with($with);
        if($this->projectId) $query = $query->where("project_id", $this->projectId);
        if(isset($filter['date'])) $query = $query->where("created_at", $filter['date']);
        if(isset($filter['status'])) $query = ($filter['status'] == ProjectFilter::ACTIVE->value) ? $query->where("active", true) : $query->where("active", false);
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function search($text, $projectId, $offset=0, $perPage=null)
    {
        $query = Package::where("project_id", $projectId);
        if($text != null) $query = $query->where("identifier", "LIKE", "%".$text."%")->orWhere("name", "LIKE", "%".$text."%");
        
        if($this->count) return $query->count();
        return $query->orderBy("created_at", "DESC")->offset($offset)->limit($perPage)->get();
    }

    public function exportToExcel($packages, $headingConfig = null)
    {
        return Excel::download(
            new PackageExport($packages, $headingConfig), 
            'packages-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportToPDF($packages, $projectName, $headingConfig = null)
    {
        $export = new PackageExport($packages, $headingConfig);
        $data = [
            'headings' => $export->headings(),
            'packages' => $packages,
            'projectName' => $projectName,
            'mappedData' => $packages->map(function ($package) use ($export) {
                return $export->map($package);
            })
        ];

        $pdf = PDF::loadView('exports.pdf.packages', $data);
        return $pdf->download('packages-' . now()->format('Y-m-d') . '.pdf');
    }
}
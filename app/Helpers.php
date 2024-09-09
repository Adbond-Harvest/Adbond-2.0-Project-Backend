<?php

namespace App;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

use App\Models\HybridStaffDraw;
use App\Models\User;
use App\Models\Notification;
use App\Models\Company_info;
use App\Models\Payment;
use App\Models\SalesOfferPayment;
use App\Models\Customer_package;

use Illuminate\Support\Facades\Http;
use App\Services\HybridStaffDrawService;
use App\Services\UserService;
usE aPP\Services\LoyaltyService;

use App\Http\Resources\MonthlyWeekDaysResource; 
use App\Http\Resources\InspectionDayMinResource;
use App\Http\Resources\VirtualStaffAssessmentResource;
use App\Http\Resources\OfferMinResource;
use App\Http\Resources\OfferBidMinResource;
use App\Http\Resources\PaymentMinResource;
use App\Http\Resources\OfferPaymentResource;
use App\Http\Resources\OrderMinResource;

use PDF;

Class Helpers
{
    public static function companyInfo()
    {
        return Company_info::first();
    }

    /*
    * Determine whether the customer Package is on active offer
    */
    public static function CustomerPackageOnOffer($customerPackageOffers)
    {
        $onOffer = false;
        if($customerPackageOffers->count() > 0) {
            foreach($customerPackageOffers as $offer) {
                if($offer->approved != -1) $onOffer = true;
            }
        }
        return $onOffer;
    }

    public static function active_promos($packageItem)
    {
        $package = $packageItem->package;
        $projectLocation = $packageItem->package?->projectLocation;
        $project = $packageItem->package?->projectLocation?->project;
        $category = $packageItem->package?->projectLocation?->project?->category;

        $promos = [];
        if($package && $package->promoProducts->count() > 0) {
            foreach($packageItem->package->promoProducts as $promoProduct) {
                if($promoProduct->promo && $promoProduct->promo->active == 1) {
                    $promos[] =  $promoProduct->promo;
                }
            }
        }
        if($projectLocation && $projectLocation->promoProducts->count() > 0) {
            foreach($projectLocation->promoProducts as $promoProduct) {
                if($promoProduct->promo && $promoProduct->promo->active == 1) {
                    $promos[] =  $promoProduct->promo;
                }
            }
        }
        if($project && $project->promoProducts->count() > 0) {
            foreach($project->promoProducts as $promoProduct) {
                if($promoProduct->promo && $promoProduct->promo->active == 1) {
                    $promos[] =  $promoProduct->promo;
                }
            }
        }
        if($category && $category->promoProducts->count() > 0) {
            foreach($category->promoProducts as $promoProduct) {
                if($promoProduct->promo && $promoProduct->promo->active == 1) {
                    $promos[] =  $promoProduct->promo;
                }
            }
        }
        return $promos;
    }

    public static function package_promos($package)
    {
        $projectLocation = $package?->projectLocation;
        $project = $package?->projectLocation?->project;
        $category = $package?->projectLocation?->project?->category;

        $promos = [];
        if($package->promoProducts && $package->promoProducts->count() > 0) {
            foreach($package->promoProducts as $promoProduct) {
                $promos[] =  $promoProduct->promo;
            }
        }
        if($projectLocation && $projectLocation->promoProducts->count() > 0) {
            foreach($projectLocation->promoProducts as $promoProduct) {
                $promos[] =  $promoProduct->promo;
            }
        }
        if($project && $project->promoProducts->count() > 0) {
            foreach($project->promoProducts as $promoProduct) {
                $promos[] =  $promoProduct->promo;
            }
        }
        if($category && $category->promoProducts->count() > 0) {
            foreach($category->promoProducts as $promoProduct) {
                $promos[] =  $promoProduct->promo;
            }
        }
        return $promos;
    }

    public static function item_price($packageItem, $customer_id=null)
    {
        $installmentDiscounts = 0;
        $fullPaymentDiscounts = 0;
        $installmentDiscount = 0;
        $fullPaymentDiscount = 0;
        $onPromo = false;
        if($packageItem->discount > 0) {
           $fullPaymentDiscounts = $packageItem->discount; 
        }
        // Get the promos attached to packageItem package
        $promos = self::active_promos($packageItem);
        if(count($promos) > 0) {
            $onPromo = true;
            foreach($promos as $promo) {
                $fullPaymentDiscounts += $promo->discount;
                $installmentDiscounts += $promo->discount;
            }
        }

        if($customer_id != null) {
            //Check for loyalty discount
            $loyaltyService = new LoyaltyService;
            $loyalty = $loyaltyService->unredeemedCustomerLoyalty($customer_id);
            if($loyalty) {
                $company = Company_info::company();
                $fullPaymentDiscounts += $company->loyalty_discount;
                $installmentDiscounts += $company->loyalty_discount; 
            }
        }

        if($fullPaymentDiscounts > 0) {
            $fullPaymentDiscount = ($fullPaymentDiscounts/100) * $packageItem->price;
        }
        if($installmentDiscounts > 0) {
            $installmentDiscount = ($installmentDiscounts/100) * $packageItem->price;
        }
        $fullPaymentAmount = $packageItem->price - $fullPaymentDiscount;
        $installmentAmount = $packageItem->price - $installmentDiscount;
        return ["fullPaymentAmount" => $fullPaymentAmount, "installmentAmount"=>$installmentAmount, "onPromo"=>$onPromo];
    }

    public static function amount_payable($packageItem, $units=1)
    {
        $amount = self::item_price($packageItem);
        // Log::stack(['project'])->info('Units：'.$units);
        $amount["fullPaymentAmount"] = ($amount["fullPaymentAmount"] * $units) + ($packageItem->infrastructure_fee * $units);
        $amount["installmentAmount"] = ($amount["installmentAmount"] * $units) + ($packageItem->infrastructure_fee * $units);
        return $amount;
    }

    public static function packageSoldOut($package)
    {
        $soldOut = true;
        if($package->items->count() > 0) {
           foreach($package->items as $item) {
               if($item->available_units > 0) $soldOut = false;
           } 
        }
        return $soldOut;
    }

    public static function kycCompleted($customer)
    {
        $completed = true;
        if(
            // $customer->photo_id == '' || $customer->photo_id == null ||
            $customer->gender == '' || $customer->gender == null ||
            $customer->marital_status == '' || $customer->marital_status == null ||
            $customer->employment_status == '' || $customer->employment_status == null ||
            $customer->occupation == '' || $customer->occupation == null ||
            $customer->postal_code == '' || $customer->postal_code == null ||
            $customer->phone_number == '' || $customer->phone_number == null ||
            $customer->address == '' || $customer->address == null ||
            $customer->age_group_id == '' || $customer->age_group_id == null ||
            // $customer->country_id == '' || $customer->country_id == null ||
            // $customer->state_id == '' || $customer->state_id == null ||
            $customer->nextOfKins->count() == 0
        ) {
            $completed = false;
        }
        return $completed;
    }

    public static function kycStarted($customer)
    {
        $started = false;
        if(
            // $customer->photo_id == '' || $customer->photo_id == null ||
            ($customer->gender != '' && $customer->gender != null) ||
            ($customer->marital_status != '' || $customer->marital_status != null) ||
            ($customer->employment_status != '' || $customer->employment_status != null) ||
            ($customer->occupation != '' || $customer->occupation != null) ||
            ($customer->postal_code != '' || $customer->postal_code != null) ||
            ($customer->phone_number != '' || $customer->phone_number != null) ||
            ($customer->address != '' || $customer->address != null) ||
            ($customer->age_group_id != '' || $customer->age_group_id != null) ||
            // $customer->country_id == '' || $customer->country_id == null ||
            // $customer->state_id == '' || $customer->state_id == null ||
            ($customer->nextOfKins && $customer->nextOfKins->count() > 0)
        ) {
            $started = true;
        }
        return $started;
    }

    public static function customerPackageUnits($customerPackage)
    {
        $units = $customerPackage->order?->units;
        if($customerPackage->purchase_type==Customer_package::$offerPurchase) {
            $units = self::customerPackageUnits($customerPackage->offer->customerPackage);
        }
        return $units;
    }

    public static function selectVirtualStaffParent()
    {
        $hybridStaffDrawService = new HybridStaffDrawService;
        $userService = new UserService;
        // Check for open draw
        $openDraw = $hybridStaffDrawService->getOpenDraw();
        // Select staffs for draw
        $idArr = $userService->selectStaffsForDraw($openDraw);

        //Select the lates fullStaff as a fall back for if there is no virtual staff to be selected from
        $staff = $userService->latestFullStaff();
        // Use the full Staff's id as default incase no virtual staff exists in the DB
        $selectedId = $staff?->id;
        if(!empty($idArr)) {
            // if there was ids to be drawn
            // Randomely select an id
            $selectedId = $idArr[array_rand($idArr)];
            $selectedUser = $userService->getUser($selectedId);
            $openDraw = ($openDraw) ? $hybridStaffDrawService->IncreaseSelected($openDraw) : $hybridStaffDrawService->openDraw(count($idArr));
            
            // Update the selected user to reflect that he/she has been selected
            $userService->update(['hybrid_staff_draw_id'=>$openDraw->id], $selectedUser);
        }
        return $selectedId;
    }

    public static function getTarget($target_type, $target)
    {
        switch($target_type) {
            case Notification::$customInspection : return new MonthlyWeekDaysResource($target); 
            case Notification::$generalInspection : return new InspectionDayMinResource($target);
            case Notification::$assessment : return new VirtualStaffAssessmentResource($target);
            case Notification::$offer : return new OfferMinResource($target);
            case Notification::$bid : return new OfferBidMinResource($target);
            case Notification::$payment : return new PaymentMinResource($target);
            case Notification::$offerPayment : return new OfferPaymentResource($target);
            case Notification::$order : return new OrderMinResource($target);
            default : return null;
        }
    }

    public static function generateReceiptNo($payment)
    {
        $start = false;
        $count = 1;
        $receiptNo = 00;
        do{
            if(!$start) {
                $receiptNo = $payment->order_id.$payment->id;
                $start = true;
            }else{
                $zeros = '';
                for($i=0; $i<$count; $i++) {
                    $zeros += '0';
                }
                $receiptNo = (int)$payment->order_id.$zeros.$payment->id;
                $count++;
            }
            $exists = Payment::where('receipt_no', $receiptNo)->first();
        }while($exists);
        return $receiptNo;
    }

    public static function generateOfferReceiptNo($payment)
    {
        $start = false;
        $count = 1;
        $receiptNo = 00;
        do{
            if(!$start) {
                $receiptNo = $payment->offer_id.$payment->id;
                $start = true;
            }else{
                $zeros = '';
                for($i=0; $i<$count; $i++) {
                    $zeros += '0';
                }
                $receiptNo = (int)$payment->order_id.$zeros.$payment->id;
                $count++;
            }
            $exists = SalesOfferPayment::where('receipt_no', $receiptNo)->first();
        }while($exists);
        return $receiptNo;
    }

    public static function getAge($dob)
    {
        $tz  = new \DateTimeZone('Africa/Lagos');
        $age = \DateTime::createFromFormat('Y-m-d', $dob, $tz)
            ->diff(new \DateTime('now', $tz))
            ->y;
        return $age;
    }

    

    public static function optimizePhoto($url)
    {
        return 'https://res.cloudinary.com/demo/image/fetch/f_auto/'.$url;
    }

    public static function eventInvite($event)
    {
        // $defaultTimeZone = date_default_timezone_get();
        //date_default_timezone_set("Africa/Lagos");
        if(!empty($event) && isset($event['name']) && isset($event['start']) && isset($event['venue']) && isset($event['description']) && isset($event['organizer_email'])) {
            $startTime = strtotime('-1 hour',strtotime($event['start']));
            $endTime = (isset($event['end'])) ? strtotime('-1 hour',strtotime($event['end'])) : null;
            // dd(date('H:i', $event['event_start']));
            $name = $event['name'];
            $location = $event['venue']; 
            $start = date('Ymd', $startTime) . 'T' . date('His', $startTime) . 'Z';
            $end = ($endTime) ? date('Ymd', $endTime) . 'T' . date('His', $endTime) . 'Z' : null;
            $description = $event['description'];
            $slug = strtolower(str_replace(array(' ', "'", '.'), array('_', '', ''), $name));

            $ical =  "BEGIN:VCALENDAR\n";
            $ical .= "VERSION:2.0\n";
            $ical .= "PRODID:-//LearnPHP.co//NONSGML {$name}//EN\n";
            $ical .= "METHOD:REQUEST\n"; // requied by Outlook
            // $ical .= "BEGIN:VTIMEZONE";
            // $ical .= "TZID:Africa/Lagos";
            // $ical .= "END:VTIMEZONE";
            $ical .= "BEGIN:VEVENT\n";
            $ical .= "UID:".date('Ymd').'T'.date('His')."-".rand()."-learnphp.co\n"; // required by Outlok
            $ical .= "DTSTAMP:".date('Ymd').'T'.date('His')."\n"; // required by Outlook
            $ical .= "DTSTART:{$start}\n"; 
            if($end) $ical .= "DTEND:{$end}\n";
            $ical .= "LOCATION:{$location}\n";
            $ical .= "SUMMARY:{$name}\n";
            $ical .= "ORGANIZER;CN=ADBOND:MAILTO:{$event['organizer_email']}\n";
            $ical .= "DESCRIPTION: {$description}\n";
            $ical .= "END:VEVENT\n";
            $ical .= "END:VCALENDAR\n";
            header("Content-Type: text/Calendar; charset=utf-8");
            header("Content-Disposition: inline; filename={$slug}.ics");
            return $ical;
        }
        return null;
    }

    public static function copy_external_file($file, $filename)
    {
        // $filename = $name.'.'.$file->extension;;
        $tempImage = tempnam(sys_get_temp_dir(), $filename);
        copy($file->secure_url, $tempImage);
        return $tempImage;
    }

    public static function removeSpecialCharacters($string)
    {
        $sanitizedString = preg_replace('/[^A-Za-z0-9 ]/', '', $string);
        return str_replace(" ", "-", $sanitizedString);
    }

    public static function getPaystackCommission($amount)
    {
        return (env('PAYSTACK_COMMISSION', 1.5)/100) * $amount;
    }

    /*
        // $ical = " BEGIN:VCALENDAR VERSION:2.0 CALSCALE:GREGORIAN BEGIN:VEVENT UID:" 
        //         . md5(uniqid(mt_rand(), true)) ."".$domain." DTSTAMP:" . gmdate('Ymd').'T'. gmdate('His') 
        //         . " DTSTART;TZID=".$dealer->timezone.":".str_replace('-', '', $date)."T".str_replace(':', '', $startTime)
        //         ." DTEND;TZID=".$dealer->timezone.":".str_replace('-', '', $date)."T".str_replace(':', '', $endTime)
        //         ." SUMMARY:".$subject
        //         ." LOCATION:".$video->FromAddress.", ".$video->FromCity." ".$video->FromPostal
        //         ." DESCRIPTION:".$desc." URL:http://www.somesite.com" 
        //         BEGIN:VALARM TRIGGER:-PT30M DESCRIPTION:Appointment Reminder ACTION:DISPLAY END:VALARM END:VEVENT END:VCALENDAR"; 
        //         header('Content-type: text/Calendar; charset=utf-8'); header('Content-Disposition: inline; filename=calendar.ics'); 
        //         echo $ical;
        // $event = array(
        //     'event_name' => 'Test Event',
        //     'event_description' => 'This is a test event. This is the description.',
        //     // mktime(hour, minute, second, month, day, year)
        //     'event_start' => strtotime("2022-10-22 11:30"),
        //     // 'event_start' => mktime(11, 00, 0, 10, 23, 2022),
        //     // 'event_end' => time() + 60*60*2,
        //     'event_venue' => array(
        //         'venue_name' => 'Test Venue',
        //         'venue_address' => '123 Test Drive',
        //         'venue_address_two' => 'Suite 555',
        //         'venue_city' => 'Some City',
        //         'venue_state' => 'Iowa',
        //         'venue_postal_code' => '12345'
        //     )
        // );
        $startTime = strtotime('-1 hour',strtotime($event['start']));
        // dd(date('H:i', $event['event_start']));
        $name = $event['event_name'];
        $venue = $event['event_venue'];
        $location = $venue['venue_name'] . ', ' . $venue['venue_address'] . ', ' . $venue['venue_address_two'] . ', ' . $venue['venue_city'] . ', ' . $venue['venue_state'] . ' ' . $venue['venue_postal_code']; 
        $start = date('Ymd', $event['event_start']) . 'T' . date('His', $event['event_start']) . 'Z';
        // $end = date('Ymd', $event['event_end']+18000) . 'T' . date('His', $event['event_end']+18000) . 'Z';
        $description = $event['event_description'];
        $slug = strtolower(str_replace(array(' ', "'", '.'), array('_', '', ''), $name));

        $ical =  "BEGIN:VCALENDAR\n";
        $ical .= "VERSION:2.0\n";
        $ical .= "PRODID:-//LearnPHP.co//NONSGML {$name}//EN\n";
        $ical .= "METHOD:REQUEST\n"; // requied by Outlook
        // $ical .= "BEGIN:VTIMEZONE";
        // $ical .= "TZID:Africa/Lagos";
        // $ical .= "END:VTIMEZONE";
        $ical .= "BEGIN:VEVENT\n";
        $ical .= "UID:".date('Ymd').'T'.date('His')."-".rand()."-learnphp.co\n"; // required by Outlok
        $ical .= "DTSTAMP:".date('Ymd').'T'.date('His')."\n"; // required by Outlook
        $ical .= "DTSTART:{$start}\n"; 
        // $ical .= "DTEND:{$end}\n";
        $ical .= "LOCATION:{$location}\n";
        $ical .= "SUMMARY:{$name}\n";
        $ical .= "DESCRIPTION: {$description}\n";
        $ical .= "END:VEVENT\n";
        $ical .= "END:VCALENDAR\n";
        header("Content-Type: text/Calendar; charset=utf-8");
        header("Content-Disposition: inline; filename={$slug}.ics");
        date_default_timezone_set($defaultTimeZone);
        return $ical;

    */

    public static function request($url, $headers=[], $post=[])
    {
        return (count($post)==0) ? Http::withHeaders($headers)->get($url)->json() : Http::withHeaders($headers)->post($url, $post)->json();
        // dd($res);
    }

    public static function wordDoc($data)
    {
        // dd(file_exists("files/WEB CONTRACT SAMPLE.doc"));
        // dd(is_readable("files/WEB CONTRACT SAMPLE.doc"));
        if(!isset($data['package']) || $data['package']==null) $data['package'] = '';
        if(!isset($data['client']) || $data['client']==null) $data['client'] = '';
        if(!isset($data['address']) || $data['address']==null) $data['address'] = '';
        if(!isset($data['size']) || $data['size']==null) $data['size'] = '';
        if(!isset($data['price']) || $data['price']==null) $data['price'] = '';
        $data['location'] = (!isset($data['location']) || $data['location']==null) ? '' : $data['location'];
        // $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("files/WEB CONTRACT SAMPLE TEMPLATE.docx");
        // $templateProcessor->setValue('day', date('jS'));
        // $templateProcessor->setValue('month', date('F'));
        // $templateProcessor->setValue('year', date('Y'));
        // $templateProcessor->setValue('package', $data['package']);
        // $templateProcessor->setValue('client', $data['client']);
        // $templateProcessor->setValue('address', $data['address']);
        // $templateProcessor->setValue('size', $data['size']);
        // $templateProcessor->setValue('price', $data['price']);
        // $templateProcessor->setValue('location', $data['location']);
        // $templateProcessor->saveAs('files/contract.docx');

        // $addressArr = [];
        // $addressArr = self::formatAddress($payment?->customer?->address);
        // $pdfData = [
        //     'image' => public_path('images/logo.PNG'),
        //     'day' => date('jS'),
        //     'month' => date('F'),
        //     'year' => date('Y'),
        //     'package' => $data['package'],
        //     'client' => $data['client'],
        //     'address' => $data['address'],
        //     'price' => $data['price'],
        //     'size' => $data['size'],
        //     'location' => $data['location']
        // ];
        // $pdf = PDF::loadView('pdf/letter_of_happiness', $pdfData);
        // // return $pdf->stream('letter_of_happiness.pdf');
        // $pdf->save('files/letter_of_happiness.pdf');
    }





    // public static function generateContract($data)
    // {
    //     if(!isset($data['project']) || $data['project']==null) $data['project'] = '';
    //     if(!isset($data['package']) || $data['package']==null) $data['package'] = '';
    //     if(!isset($data['client']) || $data['client']==null) $data['client'] = '';
    //     if(!isset($data['address']) || $data['address']==null) $data['address'] = '';
    //     if(!isset($data['state']) || $data['state']==null) $data['state'] = '';
    //     if(!isset($data['size']) || $data['size']==null) $data['size'] = '';
    //     if(!isset($data['price']) || $data['price']==null) $data['price'] = '';
    //     if(!isset($data['installment_duration']) || $data['installment_duration']==null) $data['installment_duration'] = 12;
    //     $data['location'] = (!isset($data['location']) || $data['location']==null) ? '' : $data['location'];
    //     $pdfData = [
    //         'image' => public_path('images/logo.PNG'),
    //         'day' => date('jS'),
    //         'month' => date('F'),
    //         'year' => date('Y'),
    //         'project' => $data['project'],
    //         'package' => $data['package'],
    //         'client' => $data['client'],
    //         'state' => $data['state'],
    //         'address' => $data['address'],
    //         'price' => $data['price'],
    //         'size' => $data['size'],
    //         'location' => $data['location'],
    //         'installment_duration' => $data['installment_duration'],
    //         'installment' => $data['installment']
    //     ];
    //     $pdf = PDF::loadView('pdf/contract', $pdfData);
    //     // return $pdf->stream('contract.pdf');
    //     $pdf->save('files/contract.pdf');
    // }

    // public static function generateReceipt1($payment)
    // {
    //     // dd($payment->order->amount_payable);
    //     $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor("files/receipt sample template.docx");
    //     $address1 = '';
    //     $address2 = '';
    //     $address3 = '';
    //     $addressArr = self::formatAddress($payment?->customer?->address);
    //     if(count($addressArr) > 0) {
    //         if(isset($addressArr[0])) $address1 = $addressArr[0];
    //         if(isset($addressArr[1])) $address2 = $addressArr[1];
    //         if(isset($addressArr[2])) $address3 = $addressArr[2];
    //     }
    //     $discount = 0;
    //     if($payment?->order->discounts && $payment?->order->discounts->count() > 0) {
    //         foreach($payment?->order->discounts as $orderDiscount) {
    //             $discount += $orderDiscount->discount;
    //         }
    //     }
    //     $unitSize = $payment?->order?->packageItem->size;
    //     $size = ($unitSize != null && $payment?->order?->units != null && $payment?->order?->units > 0) ? $unitSize * $payment?->order?->units : $unitSize; 
    //     $templateProcessor->setValue('name', ucfirst($payment?->customer->full_name));
    //     $templateProcessor->setValue('date', date('jS F, Y'));
    //     $templateProcessor->setValue('receiptNo', $payment->receipt_no);
    //     $templateProcessor->setValue('address1', $address1);
    //     $templateProcessor->setValue('address2', $address2);
    //     $templateProcessor->setValue('address3', $address3);
    //     $templateProcessor->setValue('paymentMethod', $payment?->mode?->name);
    //     $templateProcessor->setValue('project', $payment?->order?->packageItem?->package?->projectLocation?->project?->name);
    //     $templateProcessor->setValue('package', $payment?->order?->packageItem?->package?->name);
    //     $templateProcessor->setValue('size', $size);
    //     $templateProcessor->setValue('amount', number_format($payment->order->amount_payable));
    //     $templateProcessor->setValue('amountPaid', number_format($payment->order->amount_payed));
    //     $templateProcessor->setValue('currentAmount', number_format($payment->amount));
    //     $templateProcessor->setValue('balance', number_format($payment?->order->balance));
    //     $templateProcessor->setValue('discount', $discount);
    //     $templateProcessor->setValue('price', number_format($payment?->order?->packageItem->price));
    //     $templateProcessor->saveAs('files/receipt.docx');
    // } 

    // public static function generateLetterOfHappiness($payment)
    // {
    //     $addressArr = [];
    //     $addressArr = self::formatAddress($payment?->customer?->address);
    //     $unitSize = $payment?->order?->packageItem->size;
    //     $size = ($unitSize != null && $payment?->order?->units != null && $payment?->order?->units > 0) ? $unitSize * $payment?->order?->units : $unitSize;
    //     $pdfData = [
    //         'image' => 'logo.jpg',
    //         'name' => $payment?->customer?->full_name,
    //         'addressArr' => $addressArr,
    //         'date' => date('jS F, Y'),
    //         'package' => $payment?->order?->packageItem?->package?->name,
    //         'project' => $payment?->order?->packageItem?->package?->projectLocation?->project?->name,
    //         'location' => $payment?->order?->packageItem?->package?->projectLocation?->location?->name,
    //         'price' => $payment?->order?->amount_payable,
    //         'amount_paid' => $payment->amount,
    //         'units' => $payment?->order?->units,
    //         'size' => $size,
    //         'payment_date' => date('d/m/Y', strtotime($payment->payment_date))
    //     ];
    //     // dd($pdfData);
    //     $pdf = PDF::loadView('pdf/letter_of_happiness', $pdfData);
    //     // return $pdf->stream('letter_of_happiness.pdf');
    //     $pdf->save('files/letter_of_happiness.pdf');
    //     // dd('done');
    // }

    // public static function generateReceipt($payment)
    // {
    //     $address1 = '';
    //     $address2 = '';
    //     $address3 = '';
    //     $addressArr = self::formatAddress($payment?->customer?->address);
    //     if(count($addressArr) > 0) {
    //         if(isset($addressArr[0])) $address1 = $addressArr[0];
    //         if(isset($addressArr[1])) $address2 = $addressArr[1];
    //         if(isset($addressArr[2])) $address3 = $addressArr[2];
    //     }
    //     $discount = 0;
    //     if($payment?->order->discounts && $payment?->order->discounts->count() > 0) {
    //         foreach($payment?->order->discounts as $orderDiscount) {
    //             $discount += $orderDiscount->discount;
    //         }
    //     }
    //     $unitSize = $payment?->order?->packageItem->size;
    //     $size = ($unitSize != null && $payment?->order?->units != null && $payment?->order?->units > 0) ? $unitSize * $payment?->order?->units : $unitSize;
    //     $pdfData = [
    //         'image' => 'logo.jpg', 
    //         'name' => ucfirst($payment?->customer?->full_name),
    //         'receiptNo' => $payment->receipt_no,
    //         'address1' => $address1,
    //         'address2' => $address2,
    //         'address3' => $address3,
    //         'date' => date('jS F, Y'),
    //         'package' => $payment?->order?->packageItem?->package?->name,
    //         'project' => $payment?->order?->packageItem?->package?->projectLocation?->project?->name,
    //         'paymentMethod' => ucfirst($payment?->mode?->name),
    //         'price' => $payment?->order?->packageItem->price,
    //         'amount' => $payment->order->amount_payable,
    //         'currentAmount' => $payment->amount,
    //         'amountPaid' => $payment->order->amount_payed,
    //         'units' => $payment?->order?->units,
    //         'size' => $size,
    //         'discount' => $discount,
    //         'balance' => $payment?->order->balance,
    //     ];

    //     // dd($pdfData);
    //     $pdf = PDF::loadView('pdf/receipt', $pdfData);

    //     $pdf->setOptions(array('isRemoteEnabled' => true));
    //     // return $pdf->stream('receipt.pdf');
    //     $pdf->save('files/receipt'.$payment->receipt_no.'.pdf');
    //     // dd('done');
    // }

    // public static function generateOfferContract($data)
    // {
    //     if(!isset($data['project']) || $data['project']==null) $data['project'] = '';
    //     if(!isset($data['package']) || $data['package']==null) $data['package'] = '';
    //     if(!isset($data['client']) || $data['client']==null) $data['client'] = '';
    //     if(!isset($data['address']) || $data['address']==null) $data['address'] = '';
    //     if(!isset($data['state']) || $data['state']==null) $data['state'] = '';
    //     if(!isset($data['size']) || $data['size']==null) $data['size'] = '';
    //     if(!isset($data['price']) || $data['price']==null) $data['price'] = '';
    //     $data['location'] = (!isset($data['location']) || $data['location']==null) ? '' : $data['location'];
    //     $pdfData = [
    //         'image' => public_path('images/logo.PNG'),
    //         'day' => date('jS'),
    //         'month' => date('F'),
    //         'year' => date('Y'),
    //         'project' => $data['project'],
    //         'package' => $data['package'],
    //         'client' => $data['client'],
    //         'state' => $data['state'],
    //         'address' => $data['address'],
    //         'price' => $data['price'],
    //         'size' => $data['size'],
    //         'location' => $data['location']
    //     ];
    //     $pdf = PDF::loadView('pdf/contract', $pdfData);
    //     // return $pdf->stream('contract.pdf');
    //     $pdf->save('files/contract.pdf');
    // }

    // public static function generateOfferLetterOfHappiness($payment)
    // {
    //     $addressArr = [];
    //     $addressArr = self::formatAddress($payment?->customer?->address);
    //     $size = $payment?->offer?->packageItem?->size;
    //     $purchaseType = $payment?->offer->customerPackage->purchase_type;
    //     $units = $payment?->offer->customerPackage->order->units;
    //     if($purchaseType == Customer_package::$orderPurchase && $size != null && $units != null && $units > 0) {
    //         // if the item on offer was gotten by order and the size is not null and the units is greater than zero
    //         $size = $size * $units;
    //     }
    //     $pdfData = [
    //         'image' => 'logo.jpg',
    //         'name' => $payment?->customer?->full_name,
    //         'addressArr' => $addressArr,
    //         'date' => date('jS F, Y'),
    //         'package' => $payment?->offer?->packageItem?->package?->name,
    //         'project' => $payment?->offer?->packageItem?->package?->projectLocation?->project?->name,
    //         'location' => $payment?->offer?->packageItem?->package?->projectLocation?->location?->name,
    //         'price' => $payment?->amount,
    //         'amount_paid' => $payment->amount,
    //         'units' => $payment?->offer?->units,
    //         'size' => $size,
    //         'payment_date' => date('d/m/Y', strtotime($payment->payment_date))
    //     ];
    //     $pdf = PDF::loadView('pdf/letter_of_happiness', $pdfData);
    //     // return $pdf->stream('letter_of_happiness.pdf');
    //     $pdf->save('files/letter_of_happiness.pdf');
    // }

    // public static function generateOfferReceipt($payment)
    // {
    //     $address1 = '';
    //     $address2 = '';
    //     $address3 = '';
    //     $addressArr = self::formatAddress($payment?->customer?->address);
    //     if(count($addressArr) > 0) {
    //         if(isset($addressArr[0])) $address1 = $addressArr[0];
    //         if(isset($addressArr[1])) $address2 = $addressArr[1];
    //         if(isset($addressArr[2])) $address3 = $addressArr[2];
    //     }
    //     $discount = 0;
    //     // if($payment?->order->discounts && $payment?->order->discounts->count() > 0) {
    //     //     foreach($payment?->order->discounts as $orderDiscount) {
    //     //         $discount += $orderDiscount->discount;
    //     //     }
    //     // }
    //     $pdfData = [
    //         'image' => 'logo.jpg',
    //         'name' => ucfirst($payment?->customer?->full_name),
    //         'receiptNo' => $payment->receipt_no,
    //         'address1' => $address1,
    //         'address2' => $address2,
    //         'address3' => $address3,
    //         'date' => date('jS F, Y'),
    //         'package' => $payment?->offer?->packageItem?->package?->name,
    //         'project' => $payment?->offer?->packageItem?->package?->projectLocation?->project?->name,
    //         'paymentMethod' => ucfirst($payment?->mode?->name),
    //         // 'price' => $payment?->offer?->packageItem->price,
    //         'amount' => $payment->amount,
    //         'currentAmount' => $payment->amount,
    //         'amountPaid' => $payment->amount,
    //         'units' => $payment?->offer?->units,
    //         'size' => $payment?->offer?->packageItem->size,
    //         'discount' => $discount,
    //         'balance' => 0,
    //     ];

    //     $pdf = PDF::loadView('pdf/offer_receipt', $pdfData);
    //     // return $pdf->stream('receipt.pdf');
    //     $pdf->save('files/receipt'.$payment->receipt_no.'.pdf');
    // }

    // private static function formatAddress($address)
    // {
    //     $res = [];
    //     $addressArr = explode(' ', $address);
    //     $cutOff = 2;
    //     $reset = 0;
    //     $string = '';
    //     for($i=0; $i < count($addressArr); $i++) {
    //         $string .= $addressArr[$i];
    //         if($reset < $cutOff) {
    //             $string .= ' ';
    //         }
    //         if($reset >= $cutOff || $i == count($addressArr)) {
    //             $string .= ',';
    //             $res[] = $string;
    //             $string = '';
    //             $reset = 0;
    //         }
    //         // dd($string);
    //         $reset++;
    //     }
    //     return $res;
    // }

    public static function curl($url, $options=[], $posts=[])
    {

    }
}




?>
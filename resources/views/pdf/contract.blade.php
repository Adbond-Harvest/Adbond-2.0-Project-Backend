	
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Contract</title>
    <style type="text/css">
        body {
            font-family: Arial, sans-serif;
        }

        table td {
            text-align: center;
        }
        ol.has-sub > li {
            counter-increment: root;
        }

        ol.has-sub > li > ol.sub {
            counter-reset: subsection;
            list-style-type: none;
        }

        ol.has-sub > li > ol.sub > li {
            counter-increment: subsection;
        }

        ol.has-sub > li > ol.sub > li:before {
            content: counter(root) "." counter(subsection) " ";
        }
        .list-heading {
            margin-bottom: 5px;
        }
        li {
            margin-bottom: 5px;
        }
    </style>
    
</head>
<body>
    <div style="margin-right:10%;width: 100%; margin:0px">

        <div style="height:95%; width: 90%; border: solid thin #000; width: 100%">
            <div style="text-align: center; margin-top:15%">
                <p style="margin-bottom: 8%;"><i>DATED THIS {{$day}} DAY OF {{$month}} {{$year}}</i></p>
                <p style="margin-bottom: 8%;"><i>CONTRACT OF SALE</i></p>
                <p style="margin-bottom: 8%;"><i>BETWEEN</i></p>

                <div style="width: 80%; height: 10%; border: solid thin #000; margin-right:auto; margin-left:auto; padding-top:2%; margin-bottom: 8%; text-align:center">
                    <p style="margin-bottom: 4%;"><b>ADBOND HARVEST AND HOMES LIMITED</b></p>
                    <p><b>("VENDOR")</b></p>
                </div>

                <p style="margin-bottom: 8%;"><i>AND</i></p>

                <div style="width: 80%; height: 10%; border: solid thin #000; margin-right:auto; margin-left:auto; padding-top:2%; margin-bottom:30%; text-align:center">
                    <p style="margin-bottom: 4%;"><b>{{strtoupper($client)}}</b></p>
                    <p><b>("PURCHASER")</b></p>
                </div>

                <div style="width:90%; height:8%; margin-right:auto; margin-left:auto;">
                    <hr/>
                    <p><i>
                        IN RESPECT OF ({{number_format($size)}}SQM) LAND BEING AND ASSOCIATED AT ADBOND AGRO TO HOME, {{strtoupper($project)}}, {{strtoupper($state)}}.
                    </i></p>
                    <hr/>
                </div>
            </div>
        </div>
        
        <div style="padding-left:50px; width: 85%;">
            <p>
                <b>THIS CONTRACT OF SALE</b> is made this {{$day}} day of {{$month}}, {{$year}}
                <br/>
            </p>
            
            <p>
            <b>BETWEEN</b>
            <br/>
            </p>
            
            <p>
                <b>ADBOND HARVEST AND HOMES LIMITED</b>, a Company duly incorporated under the Companies and Allied Matters Act CAP C20 LFN 2004 with Registered Certificate (RC:1368601) and having its registered address at 14, 
                Allen Avenue suite 4-14/15, Ikeja, Lagos (hereinafter referred to as the “Vendor” which expression shall where the contexts to admits include his successors­‐in­‐title and assigns) of the one part;<br/>
            </p>
            <p>
                <b>AND</b>
            </p>
            
            <p>
                ({{$client}}) of ({{$address}}). (Herein after called the “Purchaser” which expression shall where the context so admits include its successors-­in­‐title and assigns) of the other part.
            </p>

            <p>
                The “Vendor” and the “Purchaser” are jointly referred to as the Parties.
            </p>
            
            <p>
                WHEREAS:
                <ol type="I">
                    <li>
                        The Vendor as the owner of all that parcel of land measuring approximately ({{number_format($size)}}Sqm) situate at and known as ADBOND ({{$project}}) at ({{$state}}) desires to assign his title and interest to the 
                        Purchaser over a part of parcel of land measuring approximately ({{number_format($size)}}Sqm). 
                    </li>
                    <li>
                        The Vendor has agreed to sell, assign and transfer its interest in respect of the property to the Purchaser, free from any encumbrance, and the Purchaser has agreed to purchase same from the Vendor in 
                        accordance with the terms and conditions set out in this Contract of Sale.
                    </li>
                </ol>
            </p>

            <b>THE PARTIES HAVE AGREED AS FOLLOWS:</b><br/>
            <ol class="has-sub">
                <li>
                    <b>OFFER</b><br/>
                    The Vendor has offered to sell, assign and transfer to the Purchaser, all of the Vendor’s  rights, interests, options and equity, including the unexpired term of years and any extension thereof granted 
                    to the Vendor, covering all that parcel of land measuring approximately ({{number_format($size)}}Sqm), known and referred to as ({{$location}}), and the Purchaser has agreed to purchase the Demised Property subject to 
                    the terms and conditions contained in this Contract of Sale.
                </li>
                <li>
                    <b>PURCHASE PRICE</b><br/>
                    @if($installment)
                    <p>
                        In pursuance of the said offer by the Vendor which the Purchaser hereby accepts, the Parties hereby agree that the purchase price for the Demised Property shall be the sum of (N{{number_format($price)}}) to be paid on 
                        installment for a period not more than {{$installment_duration}}months.
                    </p>
                    @else
                    <p>
                    In pursuance of the said offer by the Vendor, which the Purchaser hereby accepts, 
                    the Parties hereby agree that the purchase price for the Demised Property shall be the sum of (N{{number_format($price)}}) to be paid Outrightly.
                    </p>
                    @endif

                    <p><b>
                        NB: All land registration documentation shall be carried out in purchaser’s name and it does not include the amount paid for the land (such as survey land registration at the Ministry of Land in Ogun 
                        State and CofO at a different charge from the Ministry of Land that are usually subject to review).
                        That shall be paid separately after the purchaser have duly completed the land purchase amount.
                        <br/><br/>
                    </b></p>

                    <ol class="sub" style="margin-top: 10%;">
                        <li class="list-heading">
                            <b>LEGAL FEE</b>
                            <ol type="I">
                                <li>
                                    Without prejudice to the Purchase Price agreed by the Parties in Clause 2 above, the Vendor agrees to make all information available for the purchaser to perfect his/her title free at no 
                                    additional cost including the provisional survey plan provided by the Vendor.
                                </li>
                                <li>
                                    First payment (initial deposit payment) paid for Land Package subscribed on installments payment plan of 12-months cannot be refunded. However; the Initial deposit payment can only be 
                                    transferred for another purchase/package or resale in not more than 24-months of payment.
                                </li>
                                <li>
                                    Land package subscribed by current Subscriber who intends to reduce number of plots under his/her subscription will pay a legal fee of 10% as deduction fees on each plot relinquished.
                                </li>
                            </ol>
                        </li>
                        <li class="list-heading">
                            <b>DEVELOPMENT TERMS</b>
                            <ol type="I">
                                <li>
                                    For every Purchaser that decides to develop, Payment of Developmental Fees is Mandatory: (Fees are subject to review depending on when the Purchaser is ready and willing to develop). 
                                    However, If the Purchaser decides to resell; the new Purchaser will be under the obligation to pay the developmental fees before any form of development.
                                </li>
                                <li>
                                    The Vendor hereby undertakes to make provision for a Perimeter Fence that will safely protect and guard the whole community. Hence, no Purchaser’s individual fence will be ALLOWED.
                                </li>
                                <li>
                                    The purchaser’s structural specifications will determine the actual location of his/her portion(s) in the community vis-à-vis Duplex, Bungalow, Block of Flat or Detached House which 
                                    will be described as Zone A, B, C and D respectively.
                                </li>
                                <li>
                                    Purchasers are not permitted to bury their dead ones in the community. However, the Vendor undertakes to make provision for a burial vault/burial site to cater for the community. 
                                </li>
                            </ol>
                        </li>
                    </ol>
                </li>
                <li class="list-heading">
                    <b>GENERAL TERMS & CONDITIONS</b>
                    <ol type="I"> 
                        <li>
                            Note that in the case of resale; the Purchaser which is now refer to as “Villa Owner” will be charged 12% as transaction fee, 10% charge as documentation fee and VAT of 7.5% making a total of 
                            29.5% Fee.
                        </li>

                        <li>
                            In the event where an existing Villa Owner who wishes to relinquish the totality of his/her interest finds a prospective buyer, the charges will only be a total of 17.5% to the Vendor (ADBOND).
                        </li>

                        <li>
                            A Villa Owner’s instruction to resell will be in queue till sale is achievable after 6-months but within 18-months of instruction to resell. 
                        </li>
                        <li>
                            Kindly Note that Implementation of Allocation is done twice a Year only; March and September of the Year after all required payments have been made by the Purchaser.
                            <br/><br/>
                            * March Batch Deadline for sign up is February 25th of Every Year and Allocation is 2nd & 4th Thursdays in March.
                            <br/>
                            * September Batch Deadline for sign up is August 25th of Every Year and Allocation is 2nd & 4th Thursdays in September. 
                        <li>
                            ADBOND boasts of varying products such as Motherland, Legacy Green, My Country Home, Heritage Homesteads in different locations. In the event of any request for allocation by a Subscriber/Villa Owner the said allocation shall be granted under the product name purchased in any available location.
                        </li>
                        <li>
                            Allocation of Land to Villa Owners will kick off from the 12th to 36th Month of Property launch, and will then be ready for Physical Allocation. Villa Owners with instant request for Allocation have 100% advantage to upgrade to already developing location based on choice or place their portion of land on resales order that will happen in between 12-18months of signing up for resale subject to Clause 3(iii).
                        </li>
                        <li>
                            Note: That After 36-months of notification of the Vendor’s Readiness to allocate and the Villa Owner fails to fulfil his/her obligation as regards allocation, this failure shall attract a Management Fee per square meter of #29 for Motherland, #39 for Heritage, #49 for Legacy Green, #99 for My Country Home Per Square meter annually that will be paid as Assets Management Fee before allocation can take place.
                        </li>
                        <li>
                            Infrastructural Development Fees shall be payable Outrightly or in 6-Installments within the period of 6-months. Receipt(s) of payment should be forwarded to our representative or via our Email: info@adbondharvestandhomes.com (Infrastructural Fees varies from one location to the other at prevalent rate). 
                        </li>
                        <li>
                            If a Subscriber/Villa Owner who has being allocated his/her portion during Agricultural Development Stage with the payment of Agro Development Land Fee will be required to make an additional payment for Infrastructural Development at prevalent rate.
                        </li>
                        <li>
                            At ADBOND, Allocation is 100% Free. However, request for allocation of land will only be granted if Subscriber/Villa Owner shows clear financial readiness of instant development either at the Agricultural Development or Home Development in Less than 21days (NOTE: Allocation is Revokable).
                        </li>
                        <li>
                            At ADBOND, in the event where a Subscriber/Villa Owner neglects, fails or refuses to continue with his/her development after allocation of land; ADBOND reserves the right to withdraw/revoke the allocation of the said land after a 90-days period of abandonment. 
                        </li>
                        <li>
                            Deed of Assignment, Land of Agreement and every other alienation document shall be received by the Purchaser within 90-days of completion of payments.
                        </li>
                    </ol>
                    <br/><br/>
                </li>

                <li class="list-heading">
                    <b>MODE OF PAYMENT</b>
                    <ol type="I"> 
                        <li>
                            All payments shall be made into the Corporate Account of the Vendor as stated in the Schedule 1 below of this Agreement. 
                        </li>
                        <li>
                            Payment for sales of land are not refundable, except the purchaser is willing to resell subject to Clause 3(i, ii, iii) of the General Terms and Conditions of this Agreement. 
                        </li>
                        <li>
                            However, ADBOND must be expressly notified (written) for of every resale effected by an outgoing Villa Owner for the purpose of guidance and legal documentation. 
                        </li>
                    </ol>
                    <br/><br/>
                </li>
                <li class="list-heading">
                    <b>THE VENDOR HEREBY COVENANTS WITH THE PURCHASERS AS FOLLOWS:</b>
                    <ol class="sub">
                        <li>
                            The Vendor covenants that he has full powers and rights to sell the Demised Property to the Purchasers free from all encumbrances and hereby indemnifies the Purchasers against any reasonable loss 
                            in title related to the Demised Property.
                        </li>
                        <li>
                            The Vendor shall properly sign and execute all relevant documents to this transaction and all documents conferring title on the Purchasers.
                        </li>
                    </ol>
                </li>
                <li class="list-heading">
                    <b>THE PURCHASER HEREBY COVENANTS WITH THE VENDORS AS FOLLOWS:</b>
                    <ol class="sub">
                        <li>
                            To pay to the Vendor in the agreed manner, the Purchase Price and the Legal Fees herein stated in this Contract of Sale.
                        </li>
                        <li>
                            To pay all future levies, taxes, rates and assessments including Ground Rents, Land Use Charges, Tenement Rates, or any other Charges payable in respect of the Demised Property.
                        </li>
                        <li>
                            To pay a 20% revertible interest on the purchased price after 12months of default payment for the first year and another 20% revertible interest in the subsequent year of default payments.
                        </li>
                        <li>
                            In the event where the Purchaser fails to complete payment as stated in the preceding paragraph, the Vendor shall have every right to reconsider the Purchaser for another package/location in 
                            consonance with the amount already paid.
                        </li>
                    </ol>
                </li>
                <li class="list-heading">
                    <b>DISPUTE RESOLUTION</b>
                    <ol class="sub">
                        <li>
                            <b>Negotiation/Mediation</b><br/>
                            In the event of any dispute, amicable resolution shall be considered by both Parties with or without their legal representative(s).
                        </li>
                        <li>
                            <b>Arbitration</b><br/>
                            If at any time the Parties are unable to amicably resolve any dispute(s) through negotiation, unsatisfied party shall refer the matter to be finally settled by arbitration in accordance with 
                            the Arbitration & Conciliation Act, Cap A18, Laws of the Federation of Nigeria (LFN) 2004, by an Arbitration Committee of One (1) Arbitrator. Both parties shall appoint one Arbitrator within 
                            Fourteen (14) days of notice to commence arbitral proceedings. If parties do not agree in appointing an Arbitrator, an Arbitrator shall be appointed by the President of the Chartered Institute 
                            of Arbitrators (UK) Nigeria Branch. The Arbitration shall take place in Lagos, Nigeria and be conducted in English Language. Cost of Arbitration shall be borne in ratio 70(Unsatisfied Party) 
                            and 30(other Party). Arbitration shall be a condition precedent to applying to a court of competent jurisdiction by any of the parties. 
                        </li>
                    </ol>
                </li>
                <li class="list-heading">
                    <b>WAIVER</b><br/>
                    No waiver by either party, where the express or implied of any provision of this Agreement, or of any breach thereof, shall constitute a continuing waiver of such provision or a breach or waiver of 
                    any other provision of this Agreement.
                </li>
                <li> class="list-heading"
                    <b>SEVERABILITY</b><br/>
                    If any provision of this Agreement is invalid under any applicable statute or rule of Law, it is to that extent to be deemed omitted. The remainder of the Agreement shall be valid and enforceable to the maximum extent possible.
                </li>
                <li class="list-heading">
                    <b>NOTICES</b><br/>
                    Any notice or other communication required or permitted in this Agreement shall be in writing and shall be deemed to have been duly given when received by the other party or their agents or three (3) 
                    working days after delivery to a recognized courier company with evidence of payment for delivery. Notice may be served personally or through electronic mail transmission with confirmation, or by 
                    acknowledged courier delivery and addressed to the respective parties at the addresses set out above written or at such other addresses as may be specified by either party in writing.
                </li>
                <li class="list-heading">
                    <b>GOVERNING LAW</b><br/>
                    This Agreement shall be governed by and construed in accordance with the Laws of Federal Republic of Nigeria and the Parties have agreed to submit to the exclusive jurisdiction of Nigerian Courts.
                </li>
            </ol>

            <b><u>SCHEDULE 1 - VENDOR’S CORPORATE ACCOUNTS DETAILS</u></b>
            <br/>
            Account Name: Adbond Harvest & Homes Limited<br/>
            Bank Type: UBA    Account No.: 1019884249<br/>				
            <br/><br/>
            <p>
                <b>IN WITNESS OF WHICH</b> the Vendor and Purchaser have hereunto set their respective hands and seal the day, month and year first above written:
            </p>
            <p>
                <b>The Common Seal of the within named Vendor is hereby affixed in the presence of:</b>
            </p>

            <div style="margin-top:5px; width:30%">
            <img src="images/md-signature.jpg" />  	
            <div style="margin-top:-10px;"> 
                    <hr/>			
            </div>
                Oluwagbemiga Adekoya					
                <b>MANAGING DIRECTOR</b>
            </div>
            <div style="margin-top:55px; width:30%">
            <img src="images/ed-signature.jpg" />  	
            <div style="margin-top:-10px;"> 
                    <hr/>			
            </div>
                Joy Adebayo-Onikeku						
                <b>EXECUTIVE DIRECTOR</b>
            </div>											

            <br/><br/>
            Signed, Sealed and Delivered By the within-named <b>PURCHASER</b>
            
            <div style="margin-top:5px; padding-top:60px; width:50%">	
                <hr/>					
                <b>({{$client}})</b>
            </div>

            <div style="margin-top: 10%;">
                In the presence of: <br/><br/>
                <b>NAME:</b>	<br/><br/>
                <b>ADDRESS:</b> <br/><br/>
                <b>OCCUPATION:</b> <br/><br/>
                <b>SIGNATURE:</b>
            </div>
        </div>
        
    </div>
</body>
</html>

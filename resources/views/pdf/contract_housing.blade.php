<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.5;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .parties {
            margin-bottom: 20px;
        }
        .party {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .clause {
            margin-bottom: 15px;
        }
        .clause-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-line {
            width: 300px;
            border-top: 1px solid #000;
            margin: 20px 0;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .schedule-table, .schedule-table th, .schedule-table td {
            border: 1px solid #000;
        }
        .schedule-table th, .schedule-table td {
            padding: 5px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CONTRACT OF SALE</h1>
        <div>BETWEEN</div>
        <div class="party">ADBOND HARVEST AND HOMES LIMITED</div>
        <div>AND</div>
        <div class="party">{{ $client }}</div>
    </div>

    <div class="clause">
        THIS CONTRACT OF SALE is made this {{ $day }} day of {{ $month }} {{ $year }}
    </div>

    <div class="clause">
        BETWEEN
        <br><br>
        ADBOND HARVEST AND HOMES LIMITED of No. 14, Allen Avenue, Ikeja, Lagos State, Nigeria
        (hereinafter referred to as the "Home Developer" which expression shall where the context so admits
        include its successors-in-title and assigns) of the one part
        <br><br>
        AND
        <br><br>
        {{ $client }} of {{ $address }}, {{ $state }} State (hereinafter referred to as the "Home Owner", which expression shall where the context so admits
        include his or her successors-in-title and assigns) of the other part
    </div>

    <div class="clause">
        <div class="clause-title">1. Interpretation</div>
        <div>"Home Owner" means a purchaser of a home under this contract</div>
        <div>"Home Developer" means the seller of a home under this contract</div>
        <div>"Purchase Price" means the total cost of the home subject matter of this contract</div>
        <div>"Repossession" means the taking over of the home subject matter of this contract by the Home Developer from the Home Owner due to Default.</div>
    </div>

    <div class="clause">
        <div class="clause-title">1.1</div>
        The Home Developer has initiated a Private-driven Home Ownership Scheme to match the increasing demand for housing in the Nation and has developed a framework for a sustained Scheme. The Home Developer has developed housing communities with the intention of meeting the housing needs of the people of the Nation situated in My Country Home (MCH), Obafemi Owode Local Government Area of Ogun State, Nigeria in this contract.
    </div>

    <div class="clause">
        <div class="clause-title">1.2</div>
        The Home Developer owns the entire interest in the home located at My Country Home (MCH)
    </div>

    <div class="clause">
        <div class="clause-title">1.3</div>
        particulars of which are described and set out in Schedule I to this Contract of Sale ("the Home")
    </div>

    <div class="clause">
        <div class="clause-title">1.4</div>
        The Home Developer shall sell the home to the Home Owner and the Home Owner shall buy the home subject to the terms and conditions set forth in this contract.
    </div>

    <div class="clause">
        <div class="clause-title">1.5</div>
        The following documents shall form, be read and construed as part of this contract:
        <br>i. The Registered Deed of Assignment;
        <br>ii. The Sales Receipt; and
        <br>iii. Registered Survey Plan.
    </div>

    <div class="clause">
        <div class="clause-title">2. Purchase Price</div>
        <div class="clause-title">2.1</div>
        The Purchase Price of the property is the sum of {{ $price }} Naira Only at zero interest rate payable.
    </div>

    <div class="clause">
        <div class="clause-title">2.2</div>
        In consideration of the sum of {{ $initialDeposit }} Naira Only being commitment fee of the purchase price in accordance with ADBOND policy ("hereinafter referred to as the Initial Deposit") paid by the Home Owner to the Home Developer before the execution of this contract, (the receipt of which the Home Developer hereby acknowledges) and in pursuance of this contract, the Home Developer has agreed to sell the home to the Home Owner, and the Home Owner has agreed to purchase the home.
    </div>

    <div class="clause">
        <div class="clause-title">2.3 Balance</div>
        The Home Developer shall execute a Letter of Allotment (after a 70% sum payment of the total Purchase Price) for the purposes of paying the balance of the purchase price on a monthly basis (with a Receipt to be issued for each payment made) during the term of the Contract.
    </div>

    <div class="clause">
        <div class="clause-title">3. Conditions Precedent</div>
        <div class="clause-title">3.1</div>
        Without prejudice to any other term of this contract, it shall be a condition precedent to the continuing obligation of the Home Developer with respect to the home that during the term of this contract, the following shall always be in place each in form and substance satisfactory to the Home Developer;
        <br>i. proof and or source of income of the Home Owner.
        <br>ii. an all risk insurance policy to be taken out by the Home Owner in respect of the home during the term of the Sales Contract with an insurance company of the Home Developer's choice and the Home Developer being the first loss payee.
    </div>

    <div class="page-break"></div>

    <div class="clause">
        <div class="clause-title">4. Home Developer's Warrantees and Covenants</div>
        <div class="clause-title">4.1</div>
        The Home Developer hereby covenants with the Home Owner and undertakes that immediately upon FULL Payments; the execution of this contract he shall deliver the original title documents of the home to the order of the Home Owner.
    </div>

    <div class="clause">
        <div class="clause-title">4.2</div>
        The Home Developer hereby warrants that there are no subsisting third party rights, interest and charges whatsoever existing or attached to the home hereinbefore described and hereby covenants with the Home Owner to indemnify and keep indemnified the Home Owner against all losses, damages or detriments whatsoever caused or to be incurred in consequence of any action or anything in any way done by any rival or adverse claimant in respect of the home.
    </div>

    <div class="clause">
        <div class="clause-title">4.3</div>
        For Outright payments plan; The Home Developer after completion of all payments by the Home Owner shall deliver the home within 12-months for proper handing-over.
    </div>

    <div class="clause">
        <div class="clause-title">4.4</div>
        For Instalments payment plan; The Home Developer after completion of all payments by the Home Owner shall deliver the home within 18-months for proper handing-over.
    </div>

    <div class="clause">
        <div class="clause-title">5. Home Owner's Warrantees and Covenants</div>
        <div class="clause-title">5.1</div>
        The Home Owner warrants that prior to the execution of this contract, he/she is not an owner, either solely or jointly, of a home anywhere within the territory of MCH, Ogun State or a beneficiary of an allocation of any State Land in Ogun State and covenants that he/she shall immediately before the execution of this contract depose to a Buyer's Affidavit in this regard in the form contained in Schedule 2 to this contract.
    </div>

    <div class="clause">
        <div class="clause-title">5.2</div>
        The Buyer's Affidavit is a condition precedent to the completion of this contract and shall form a part of this contract.
    </div>

    <div class="clause">
        <div class="clause-title">5.3</div>
        The Home Owner warrants that he/she shall rent/reside in the home solely or with his immediate or extended family and further covenants that in no circumstances shall he transfer, pledge, charge, let or otherwise assign his interest in the home to a third party for a consideration or otherwise during the term of the Contract without the written consent of the Home Developer having first been sought and obtained.
    </div>

    <div class="clause">
        <div class="clause-title">6. Repossession and Termination.</div>
        <div class="clause-title">6.1</div>
        The Home Owner hereby covenants that any false deposition in the Buyer's Affidavit shall immediately entitle the Home Developer to terminate this contract and repossess the home without prejudice to any criminal prosecution that such false deposition may occasion.
    </div>

    <div class="clause">
        <div class="clause-title">6.2</div>
        The Home owner further covenants that a breach of Clause 5.3 herein shall immediately entitle the Home Developer to terminate this contract and repossess the home notwithstanding any other rights that such a breach may confer on the Home Developer.
    </div>

    <div class="clause">
        <div class="clause-title">6.3</div>
        Parties hereby covenant that in the event of Repossession, the Home Owner shall be entitled ONLY to a Reallocation to another Apartment/Block within the next Housing project of ADBOND (THE Home Developer) of his/her Payment Deposits and any such monthly payment made towards the balance of the purchase price at the time of repossession/termination subject to any penalty or deduction.
    </div>

    <div class="page-break"></div>

    <div class="clause">
        <div class="clause-title">6.4</div>
        In a case where the Home Owner intend to terminate this contract; a Resale-Order will be advised and a new Home Owner will be sought before refund and this will happen in less that 365-days duration.
    </div>

    <div class="clause">
        <div class="clause-title">6.5</div>
        In the repossession/reallocation of the equity contribution pursuant to Clause 6.3 above, reference shall be had only to the capital value as at the date of contracting or the current market value whichever is less.
    </div>

    <div class="clause">
        <div class="clause-title">6.6</div>
        Notwithstanding anything to the contrary in this contract or any other agreement, the Home Developer during the term of this contract or any other such agreement reserves the right to alter, amend or issue new regulations or framework for the operation and administration of the My Country Home Ownership Scheme.
    </div>

    <div class="clause">
        <div class="clause-title">6.7</div>
        There will be a 10% (of the monthly payment) as additional interest added for every month defaulted and will be compounded as fine for more that one month as well.
    </div>

    <div class="clause">
        <div class="clause-title">6.8</div>
        If the total duration of payment is due and payments not completed; all already paid amount will be moved to the next ADBOND Housing Project Development within the State selected.
    </div>

    <div class="clause">
        <div class="clause-title">7. Deed of Assignment</div>
        <div class="clause-title">7.1</div>
        Upon the execution of this contract, the Home Owner shall immediately execute a Deed of Assignment with ADBOND in the form contained in Schedule 1 to this contract for the purpose of paying the balance of the purchase price subject to the documentations of the Home Ownership.
    </div>

    <div class="clause">
        <div class="clause-title">8. Vacant Possession</div>
        The Home Developer shall grant vacant possession of the home to the Home Owner immediately upon execution of the Deed of Assignment and the certification of the home as being fit for habitation.
    </div>

    <div class="clause">
        <div class="clause-title">9. Facility Management</div>
        <div class="clause-title">9.1</div>
        There shall be a Facility Management company to be appointed by the Home Developer whose responsibility shall include the proper management and maintenance of the homes and provide facility management services within the Community known as My Country Home (MCH) Ogun State.
    </div>

    <div class="clause">
        <div class="clause-title">9.2</div>
        The Scope of the Facility Management services shall include the provision of a Facility management framework for the purpose of ensuring effective management of the homes and environment;
    </div>

    <div class="clause">
        <div class="clause-title">9.3</div>
        The Home Owner shall be required to pay a monthly/quarterly/annual maintenance charge to the Facility Management company appointed by the Home Developer for the services provided by the Facility Management company which sum shall be agreed in advance by the parties.
    </div>

    <div class="clause">
        <div class="clause-title">9.4</div>
        Non-payment of the monthly/quarterly/annual maintenance charge as and when due shall have the same effect and consequence as non-payment of monthly mortgage re-payments as and when due.
    </div>

    <div class="page-break"></div>

    <div class="clause">
        <div class="clause-title">10. Notices</div>
        Any notice or communication to any party shall be deemed to be sufficient, if it is delivered by hand or sent by courier service or by email to the principal place of business or other address earlier notified in writing by the party to whom the notice or communication is required to be given.
    </div>

    <div class="clause">
        <div class="clause-title">11. The Benefit to Home Owner</div>
        The Home Developer will after completion of all payments stated in this contract shall allow the Home Owner gets the following as benefit of being an Home Owner:
        <br>a. A Mountain Bicycle
        <br>b. Mini Library in each Flat Apartment
        <br>c. Motherland Homestead 600Sqm + Free Cassava Cultivation in your Child/ren's Name.
        <br>d. Generational Lifetime Rental Income Management
        <br>e. Annual Rental Earning of ¥lm/Flat
        <br>f. Pay Outrightly and Enjoy Further 10% Discounts; OR
        <br>g. Pay Instalments within 12-months and Enjoy 5% Discounts
    </div>

    <div class="clause">
        <div class="clause-title">12. The Contract</div>
        This contract shall remain in effect until the {{ $endDay }} day of {{ $endMonth }} {{ $endYear }} or such other period as may be agreed by the parties when the Home Owner would have fulfilled all his or her obligations under the Contract and under any other document as may be required by the Home Developer.
    </div>

    <div class="clause">
        <div class="clause-title">13. Dispute Resolution</div>
        <div class="clause-title">13.1</div>
        Any dispute or differences arising out of or relating to this contract shall be resolved exclusively by arbitration conducted in accordance with the Arbitration Law of Nigeria.
    </div>

    <div class="clause">
        <div class="clause-title">13.2</div>
        The arbitration shall be in Nigeria and shall be by a single arbitrator appointed by the President of the Ogun Court of Arbitration after a request for reference is made by either party.
    </div>

    <div class="clause">
        <div class="clause-title">13.3</div>
        The arbitration shall be conducted under the ADBOND Home Ownership Scheme Housing Arbitration Rules contained in Schedule 1 to this contract.
    </div>

    <div class="clause">
        <div class="clause-title">13.4</div>
        The arbitrator shall apply Nigerian law.
    </div>

    <div class="clause">
        <div class="clause-title">13.5</div>
        The costs of the arbitration shall be shared equally between the parties herein.
    </div>

    <div class="clause">
        <div class="clause-title">13.6</div>
        The decision of the arbitrator shall be final and binding. By executing this contract, the parties hereby agree that in the event of any dispute arising herefrom, there shall be no recourse to court for the purpose of seeking an injunctive relive or any relief whatsoever except for the purpose of enforcing the arbitral award.
    </div>

    <div class="signature-section">
        <div>SIGNED SEALED AND DELIVERED BY THE REPRESENTATIVE OF THE WITHIN NAMED HOME DEVELOPER:</div>
        <br>
        <div style="width: 50%; float: left;">
            <div>DIRECTOR</div>
            <div class="signature-line"></div>
        </div>
        <div style="width: 50%; float: left;">
            <div>EXECUTIVE SECRETARY</div>
            <div class="signature-line"></div>
        </div>
        <div style="clear: both;"></div>
        <br><br>
        <div>Signed, sealed and delivered by the within named Home Owner</div>
        <div class="signature-line" style="width: 50%;"></div>
        <div>In the presence of:</div>
        <br>
        <div>Name: ___________________________</div>
        <div>Occupation: ___________________________</div>
        <div>Address: ___________________________</div>
        <div>Signature: ___________________________</div>
    </div>

    <div class="page-break"></div>

    <div class="header">
        <h1>SCHEDULE I</h1>
    </div>

    <table class="schedule-table">
        <tr>
            <th>DESCRIPTION</th>
            <th>PARTICULARS</th>
        </tr>
        <tr>
            <td>ALL that entire Flat/Apartment situate at {{ $propertyLocation }} In Obafemi Owode Local Government Area of Ogun State.</td>
            <td>Covered by {{ $deedNumber }} dated {{ $deedDay }} Day of {{ $deedMonth }} {{ $deedYear }} and registered as No. {{ $registrationNumber }} at page {{ $pageNumber }} in volume {{ $volumeNumber }} of the Lands Registry in the office at Ogun State.</td>
        </tr>
    </table>

    <div class="page-break"></div>

    <div class="header">
        <h1>SCHEDULE 2</h1>
        <h2>BUYER'S AFFIDAVIT</h2>
    </div>

    <div class="clause">
        I (Full names) {{ $affiantName }} (gender: {{ $affiantGender }}, occupation: {{ $affiantOccupation }}, nationality: {{ $affiantNationality }}) of (full address not P.O. Box) {{ $affiantAddress }}, Ogun State, do hereby make oath and state as follows:
        <br><br>
        1. I am the above described person and I am applying for the purchase of a {{ $propertyType }} at {{ $propertyLocation }} under the ADBOND Housing Ownership Scheme.
        <br><br>
        2. I declare that all the information provided by me in the Application Form is true.
        <br><br>
        3. I declare that I do not own any home, neither am I the beneficiary of any allocation of State land anywhere within the territory of Lagos State and that I am a home buyer in accordance with the principles and intention of this Scheme.
        <br><br>
        4. I will subscribe to all the rules, regulations, terms and conditions contained in the Contract of Sale, the Housing Arbitration Rules and any other document as may be required for the operation and or administration of the this Housing Scheme.
        <br><br>
        5. I understand and agree that in deposing to this affidavit, it becomes part of my contract with ADBOND and that any false deposition will disentitle me to benefit from the Project and should I have already benefitted, will entitle ADBOND at any time to revoke the allocation to me and also impose any other applicable sanctions.
        <br><br>
        6. I depose to this affidavit in good faith and in accordance with the Oaths Law of Nigeria.
    </div>

    <div class="signature-section" style="margin-top: 50px;">
        <div><strong>DEPONENT</strong></div>
        <div class="signature-line" style="width: 50%;"></div>
        <br>
        <div>SWORN TO AT ________________________ THIS {{ $affidavitDay }} DAY OF {{ $affidavitMonth }} {{ $affidavitYear }}</div>
        <br><br>
        <div><strong>BEFORE ME</strong></div>
        <div>NAME & SIGNATURE</div>
        <div>(COMMISSIONER FOR OATHS OR NOTARY PUBLIC)</div>
        <div class="signature-line" style="width: 50%;"></div>
    </div>
</body>
</html>
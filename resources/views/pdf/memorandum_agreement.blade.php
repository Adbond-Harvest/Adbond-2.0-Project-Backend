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
            text-transform: uppercase;
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
        .page-break {
            page-break-after: always;
        }
        .prepared-by {
            margin-top: 50px;
            font-style: italic;
        }
        .center {
            text-align: center;
        }
        .underline {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>MEMORANDUM</h1>
        <h1>OF</h1>
        <h1>UNDERSTANDING</h1>
        <div>BETWEEN</div>
        <div class="party">ADBOND HARVEST AND HOMES LIMITED</div>
        <div>AND</div>
        <div class="party">{{ $client }}</div>
    </div>

    <div class="page-break"></div>

    <div class="header">
        <h1>MEMORANDUM OF UNDERSTANDING</h1>
    </div>

    <div class="clause">
        <strong>THIS MEMORANDUM OF UNDERSTANDING</strong> (M.O.U) is made the {{ $day }} day of {{ $month }}, {{ $year }}, BETWEEN <strong>ADBOND HARVEST AND HOMES LIMITED</strong> 
        of ADDRESS NO. 14, ALLEN AVENUE, IKEJA, LAGOS STATE, NIGERIA (hereinafter referred to as "the 1st Party" which expression shall where the context so admits, include his heirs, assigns, executors and administrators) of the one part 
        AND <strong>{{ $client }}</strong> of ADDRESS {{ $address }}, (hereinafter referred to as "the 2nd Party" which expression shall where the context so admits, include his heirs, assigns, executors and administrators) of the other part.
    </div>

    <div class="clause">
        <div class="clause-title">WHEREAS:</div>
        <div class="clause-title">1.</div>
        The 1st and 2nd Party (hereinafter referred to as "the Parties") have agreed to carry on business under the name and style of "ADBOND", and to engage in the investment of Asset Purchase (hereinafter called "Land").
    </div>

    <div class="clause">
        <div class="clause-title">2.</div>
        In pursuance of the aforesaid agreement, the Parties shall carry on the investment, subject to the terms and conditions as stipulated in this M.O.U.
    </div>

    <div class="clause">
        <div class="clause-title">NOW THIS M.O.U. WITNESSES AS FOLLOWS:</div>
        <div class="clause-title">1.</div>
        The Parties shall be deemed to have commenced the investment purchase on the date this M.O.U is executed and shall continue from that date until determined as hereinafter provided.
    </div>

    <div class="clause">
        <div class="clause-title">2.</div>
        The investment shall be carried on in Nigeria or at such other place or places as the Parties may from time to time agree upon.
    </div>

    <div class="page-break"></div>

    <div class="clause">
        <div class="clause-title">3.</div>
        The 1st Party shall provide the sum Asset (Land) for purchase as the Investment as the Vendor (Seller).
    </div>

    <div class="clause">
        <div class="clause-title">4.</div>
        The 2nd Party shall provide payment in the sum of {{ $price }}, representing the obligation as the Villa Owner (Buyer).
    </div>

    <div class="clause">
        <div class="clause-title">5.</div>
        The Parties shall agree on a payment plan which could be Outright Purchase Payment Plan OR Installment Purchase Payment Plan.
    </div>

    <div class="clause">
        <div class="clause-title">6.</div>
        The 1st Party shall give all necessary document as evidence of Purchase after ALL Payments is made by the 2nd Party as record purpose of all the asset(s), and the same shall be signed by both Parties, 
        and be binding on them unless some manifest error shall be found by them in which case, the same shall be rectified accordingly.
    </div>

    <div class="clause">
        <div class="clause-title">7.</div>
        Each Party shall at all times:
        <br>(i) show the utmost good faith to the other in all matters relating to the investment; and
        <br>(ii) dutifully perform his obligations, covenants and conditions under this M.O.U.
    </div>

    <div class="clause">
        <div class="clause-title">8.</div>
        Any Party who intends to terminate this M.O.U shall give the other Party at least three months' notice in writing of his intention to terminate same.
    </div>

    <div class="clause">
        <div class="clause-title">9.</div>
        Any dispute arising out of this M.O.U between the Parties or their respective representatives or between one Party and the representatives of the other Party shall be referred to Arbitration, 
        in accordance with the provisions of the Arbitration and Conciliation Act, Laws of the Federation of Nigeria.
    </div>

    <div class="page-break"></div>

    <div class="signature-section center">
        <div>SIGNED, SEALED AND DELIVERED BY THE WITHIN NAMED 1ST PARTY:</div>
        <br><br><br><br>
        <div class="signature-line"></div>
        <div>1ST PARTY</div>
        <br>
        <div>In the presence of:</div>
        <br>
        <div>Name: ___________________________</div>
        <div>Address: ___________________________</div>
        <div>Occupation: ___________________________</div>
        <br><br>
        <div class="signature-line"></div>
        <div>WITNESS</div>
    </div>

    <div class="signature-section center">
        <div>SIGNED, SEALED AND DELIVERED BY THE WITHIN NAMED 2ND PARTY</div>
        <br><br><br><br>
        <div class="signature-line"></div>
        <div>2ND PARTY</div>
        <br>
        <div>In the presence of:</div>
        <br>
        <div>Name: ___________________________</div>
        <div>Address: ___________________________</div>
        <div>Occupation: ___________________________</div>
        <br><br>
        <div class="signature-line"></div>
        <div>WITNESS</div>
    </div>

    <div class="page-break"></div>

    <div class="prepared-by center">
        <div class="underline">Prepared by:</div>
        <br>
        <div><strong>OLUWASEUN BANKOLE ESQ.,LLB.,BL.,AClarb</strong></div>
        <div><strong>SLEEK ATTORNEYS & SOLICITORS</strong></div>
        <div>Barristers, Solicitors & Property Consultants</div>
        <br>
        <div>No 6, Soji Adepegba Street,</div>
        <div>Allen Avenue, Ikeja, Lagos State.</div>
        <div>Tel: 07033517227, 09038214410</div>
        <div>E-mail: sleekattorneys@yahoo.com</div>
    </div>
</body>
</html>
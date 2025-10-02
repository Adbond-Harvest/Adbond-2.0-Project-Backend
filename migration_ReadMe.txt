ignore these tables 
    - Banks
    - bank_payment_reference 
    - offer_bank_payment_reference
    - card_payment_channels
    - Categories
    - Cities
    - Comment_replies,
    - commission_rates
    - countries
    - customer_documents
    - Features


Hybrid_staff_draws

TODO

- company_info
- customer_next_of_kin
- projects
- monthly_week_days : use project_location_id to get the project name and find it in v2, then use monthly_week_days to find the inspection date and 
then get the time, then use all these to create a new inspection


- packages & package_items
- package_photos
- orders

-user_commissions
- user_commission_payments

 projects->projectLocation->packages->orders->payments->
                                            ->orderClientPackages->orderOffers->offerCLientPackages->offerOffers
                                    ->packagePhotos->

     migrateOrderClientPackages->migrateOrderOffers->migrateOfferClientPackages->migrateOfferOffers
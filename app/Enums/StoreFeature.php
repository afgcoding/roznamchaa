<?php

namespace App\Enums;

enum StoreFeature: string
{
    /** Boxes/Pieces purchase→sale unit conversion */
    case MultiUnitConversion = 'multi_unit_conversion';

    /** Purchase-unit price helpers that derive cost per sale unit */
    case WholesalePricing = 'wholesale_pricing';

    /** Customer credit limit field + over-limit alerts */
    case CreditLimit = 'credit_limit';

    /** Delivery challan printing (future) */
    case DeliveryChallan = 'delivery_challan';

    /** Product expiry date alerts (future) */
    case ExpiryAlerts = 'expiry_alerts';

    /** Multi-cashier shifts (future) */
    case CashierShifts = 'cashier_shifts';

    /** Advanced POS shortkeys (future) */
    case PosShortkeys = 'pos_shortkeys';

    /** Sale discount engine */
    case DiscountEngine = 'discount_engine';
}

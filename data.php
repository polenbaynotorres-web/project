<?php
/**
 * data.php — shared content used across multiple pages.
 * Keeping this in one place means updating a car's price, or adding
 * a testimonial, only has to happen once instead of once per page.
 *
 * Each car's "colors" list is its base catalog of paint options. Which
 * of those colors are actually IN STOCK right now is stored in the
 * database (vehicle_colors table) and only an admin can change it —
 * see admin.php. fleet.php/index.php cross-reference the two.
 */

/** Turns a car name into a URL/DB-safe slug, e.g. "Toyota Vios" -> "toyota-vios". */
function car_slug($name) {
    $slug = strtolower($name);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    return trim($slug, '-');
}

$siteName = "Torres Vehicle Rental";

/**
 * Pick-up/return locations offered site-wide. Keys are stored in the
 * database and used in URLs/forms; values are the human-readable labels.
 * Defined once here so the home page search bar, the reservation modal,
 * and server-side validation all stay in sync.
 */
$locations = [
    "dumaguete" => "Dumaguete City",
    "cebu"      => "Cebu City",
    "manila"    => "Manila",
    "davao"     => "Davao City",
];

$fleet = [
    [
        "name"  => "Toyota Vios",
        "seats" => 5,
        "price" => 1800,
        "class" => "car-sedan",
        "img"   => "img/VIOS-cropped.png",
        "colors" => [
            ["name" => "Red",   "img" => "img/VIOS-cropped.png"],
            ["name" => "Black", "img" => "img/VIOS-BLACK-cropped.png"],
            ["name" => "Blue",  "img" => "img/VIOS-BLUE-cropped.png"],
            ["name" => "Grey",  "img" => "img/VIOS-GREY-cropped.png"],
            ["name" => "White", "img" => "img/VIOS-WHITE-cropped.png"],
        ],
    ],
    [
        "name"  => "Toyota Innova",
        "seats" => 7,
        "price" => 2500,
        "class" => "car-mpv",
        "img"   => "img/INNOVA-cropped.png",
        "colors" => [
            ["name" => "Silver", "img" => "img/INNOVA-cropped.png"],
            ["name" => "Black",  "img" => "img/INNOVA-BLACK-cropped.png"],
            ["name" => "Maroon", "img" => "img/INNOVA-MAROON-cropped.png"],
        ],
    ],
    [
        "name"  => "Fortuner",
        "seats" => 7,
        "price" => 3500,
        "class" => "car-suv",
        "img"   => "img/FORTUNER-cropped.png",
        "colors" => [
            ["name" => "Black", "img" => "img/FORTUNER-cropped.png"],
            ["name" => "Blue",  "img" => "img/FORTUNER-BLUE-cropped.png"],
        ],
    ],
    [
        "name"  => "Hiace Commuter",
        "seats" => 15,
        "price" => 3800,
        "class" => "car-van",
        "img"   => "img/HIACE-cropped.png",
        "colors" => [
            ["name" => "White",     "img" => "img/HIACE-cropped.png"],
            ["name" => "Red",       "img" => "img/HIACE-RED-cropped.png"],
            ["name" => "Off-White", "img" => "img/HIACE-OFFWHITE-cropped.png"],
        ],
    ],
    [
        "name"  => "Honda Civic",
        "seats" => 5,
        "price" => 2000,
        "class" => "car-hatchback",
        "img"   => "img/CIVIC-BLACK-cropped.png",
        "colors" => [
            ["name" => "Black", "img" => "img/CIVIC-BLACK-cropped.png"],
            ["name" => "Blue",  "img" => "img/CIVIC-BLUE-cropped.png"],
        ],
    ],
    [
        "name"  => "Mitsubishi Xpander",
        "seats" => 7,
        "price" => 2300,
        "class" => "car-mpv",
        "img"   => "img/XPANDER-WHITE-cropped.png",
        "colors" => [
            ["name" => "White",  "img" => "img/XPANDER-WHITE-cropped.png"],
            ["name" => "Black",  "img" => "img/XPANDER-BLACK-cropped.png"],
            ["name" => "Red",    "img" => "img/XPANDER-RED-cropped.png"],
            ["name" => "Orange", "img" => "img/XPANDER-ORANGE-cropped.png"],
        ],
    ],
    [
        "name"  => "Isuzu D-Max",
        "seats" => 5,
        "price" => 2800,
        "class" => "car-pickup",
        "img"   => "img/DMAX-WHITE-cropped.png",
        "colors" => [
            ["name" => "White", "img" => "img/DMAX-WHITE-cropped.png"],
            ["name" => "Blue",  "img" => "img/DMAX-BLUE-cropped.png"],
            ["name" => "Grey",  "img" => "img/DMAX-GREY-cropped.png"],
            ["name" => "Red",   "img" => "img/DMAX-RED-cropped.png"],
        ],
    ],
];

$features = [
    ["icon" => "tag",      "title" => "No Hidden Fees",       "desc" => "What you see is what you pay. All-in pricing, always."],
    ["icon" => "calendar", "title" => "Flexible Cancelation",  "desc" => "Free cancellation within the allowed window."],
    ["icon" => "key",      "title" => "Contactless Pick-Up",   "desc" => "Grab your keys safely and start your journey."],
    ["icon" => "gauge",    "title" => "No Mileage Caps",       "desc" => "Drive worry-free with no hidden mileage limits."],
    ["icon" => "doc",      "title" => "Instant Digital Agreement", "desc" => "Sign, confirm, and go — 100% digital, 100% convenient."],
    ["icon" => "shield",   "title" => "24/7 Roadside Support", "desc" => "We're here for you, anytime, anywhere."],
];

$steps = [
    ["num" => "01", "title" => "Choose Your Vehicle",  "desc" => "Browse our fleet and select the perfect ride for you."],
    ["num" => "02", "title" => "Pick Your Date",        "desc" => "Select your pick-up and return dates and time."],
    ["num" => "03", "title" => "Confirm Booking",       "desc" => "Review the price and terms, then confirm booking."],
    ["num" => "04", "title" => "Pick-Up & Drive",       "desc" => "Pick up your vehicle and enjoy a safe, smooth journey."],
];

$testimonials = [
    ["name" => "Jessica Reyes",   "role" => "Family Traveler",    "quote" => "Torres made our family trip completely stress-free. The car was clean, the fuel was full, and the staff were amazing.", "rating" => 5],
    ["name" => "Marcus Feld",     "role" => "Business Traveler",  "quote" => "Booking was so easy and transparent. The vehicle was exactly what we booked, no surprises at all.", "rating" => 5],
    ["name" => "Anna Dela Cruz",  "role" => "Road Tripper",       "quote" => "Great service from start to finish. Their 24/7 support gave us peace of mind on the road.", "rating" => 5],
    ["name" => "Paolo Santos",    "role" => "Weekend Traveler",   "quote" => "Picked up the Fortuner for a weekend trip and it drove like new. Pick-up took less than 10 minutes.", "rating" => 5],
    ["name" => "Kimberly Ong",    "role" => "First-Time Renter",  "quote" => "I was nervous renting for the first time but the staff walked me through everything. Would book again.", "rating" => 4],
    ["name" => "Ramon Villareal", "role" => "Frequent Renter",    "quote" => "I rent from Torres almost every month for work trips. Consistent, reliable, and fairly priced.", "rating" => 5],
];
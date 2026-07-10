<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Farm;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Product;
use App\Models\Innovation;
use App\Models\Competition;
use App\Models\Disease;
use App\Models\DiseaseAlert;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DistrictSeeder::class);
        $this->command->info("🌱 Seeding AgriTech Pro database...");

        $this->seedUsers();
        $this->seedInstructors();
        $this->seedCourses();
        $this->seedProducts();
        $this->seedDiseases();
        $this->seedDiseaseAlerts();
        $this->seedInnovations();
        $this->seedCompetition();

        $this->command->info("✅ Database seeding complete!");
    }

    // ── Users ─────────────────────────────────────────────────────────

    private function seedUsers(): void
    {
        $this->command->info("  → Seeding users...");

        // Admin user
        $admin = User::firstOrCreate(
            ["email" => "admin@agritechpro.mw"],
            [
                "first_name" => "Admin",
                "last_name" => "User",
                "phone" => "+265999000000",
                "password" => Hash::make("password"),
                "role" => "admin",
                "status" => "active",
                "district" => "Blantyre",
            ],
        );

        // Sample farmers
        $farmers = [
            [
                "John",
                "Mutale",
                "+265991234567",
                "john@example.com",
                "Lilongwe",
                "small_scale",
                "Maize, Soybeans",
                4.5,
            ],
            [
                "Grace",
                "Nkosi",
                "+265992345678",
                "grace@example.com",
                "Blantyre",
                "commercial",
                "Tomatoes, Vegetables",
                12.0,
            ],
            [
                "Charles",
                "Banda",
                "+265993456789",
                "charles@example.com",
                "Zomba",
                "mixed",
                "Maize, Cattle",
                8.0,
            ],
            [
                "Mary",
                "Phiri",
                "+265994567890",
                "mary@example.com",
                "Kasungu",
                "organic",
                "Soybeans, Groundnuts",
                3.5,
            ],
            [
                "Peter",
                "Tembo",
                "+265995678901",
                "peter@example.com",
                "Mzimba",
                "livestock",
                "Cattle, Goats",
                20.0,
            ],
            [
                "Agnes",
                "Chirwa",
                "+265996789012",
                "agnes@example.com",
                "Salima",
                "small_scale",
                "Rice, Maize",
                2.0,
            ],
            [
                "David",
                "Mwale",
                "+265997890123",
                "david@example.com",
                "Dedza",
                "commercial",
                "Potatoes, Cabbages",
                15.0,
            ],
            [
                "Sarah",
                "Gondwe",
                "+265998901234",
                "sarah@example.com",
                "Ntcheu",
                "mixed",
                "Maize, Tobacco",
                6.5,
            ],
        ];

        foreach (
            $farmers
            as [$first, $last, $phone, $email, $district, $type, $crops, $size]
        ) {
            $user = User::firstOrCreate(
                ["email" => $email],
                [
                    "first_name" => $first,
                    "last_name" => $last,
                    "phone" => $phone,
                    "password" => Hash::make("password"),
                    "role" => "farmer",
                    "status" => "active",
                    "district" => $district,
                    "sms_alerts" => true,
                    "total_courses_enrolled" => rand(1, 5),
                    "total_orders" => rand(0, 8),
                    "total_innovations" => rand(0, 2),
                ],
            );

            Farm::firstOrCreate(
                ["user_id" => $user->id],
                [
                    "name" => "{$first}'s Farm",
                    "farm_type" => $type,
                    "size_hectares" => $size,
                    "district" => $district,
                    "primary_crops" => explode(", ", $crops),
                    "irrigation_type" => ["rain_fed", "drip", "borehole"][
                        rand(0, 2)
                    ],
                    "is_verified" => rand(0, 1) === 1,
                ],
            );
        }

        $this->command->info("    ✓ " . User::count() . " users seeded");
    }

    // ── Instructors ───────────────────────────────────────────────────

    private function seedInstructors(): void
    {
        $this->command->info("  → Seeding instructors...");

        $instructors = [
            [
                "name" => "Dr. James Mwale",
                "title" => "Dr.",
                "specialization" => "Soil Science & Crop Production",
                "bio" =>
                    "20+ years experience in maize production research at CIMMYT and Malawi Ministry of Agriculture.",
            ],
            [
                "name" => "Prof. Grace Banda",
                "title" => "Prof.",
                "specialization" => "Animal Science & Livestock Management",
                "bio" =>
                    "Professor of Animal Science at Lilongwe University of Agriculture and Natural Resources (LUANAR).",
            ],
            [
                "name" => "Eng. Peter Chirwa",
                "title" => "Eng.",
                "specialization" => "Agricultural Engineering & Irrigation",
                "bio" =>
                    "Agricultural engineer specializing in small-scale drip and sprinkler irrigation systems for smallholder farmers.",
            ],
            [
                "name" => "Dr. Agnes Phiri",
                "title" => "Dr.",
                "specialization" => "Plant Pathology & Disease Management",
                "bio" =>
                    "Plant pathologist with expertise in crop disease identification and integrated pest management.",
            ],
            [
                "name" => "Mr. Charles Tembo",
                "title" => "Mr.",
                "specialization" => "Agribusiness & Farm Management",
                "bio" =>
                    "Certified agribusiness consultant who has helped 500+ farmers grow profitable farming enterprises.",
            ],
            [
                "name" => "Mrs. Mary Gondwe",
                "title" => "Mrs.",
                "specialization" => "Organic Farming & Certification",
                "bio" =>
                    "Organic farming specialist and certified trainer with GlobalG.A.P. and USAID experience.",
            ],
        ];

        foreach ($instructors as $data) {
            Instructor::firstOrCreate(
                ["name" => $data["name"]],
                array_merge($data, ["is_active" => true]),
            );
        }

        $this->command->info(
            "    ✓ " . Instructor::count() . " instructors seeded",
        );
    }

    // ── Courses ───────────────────────────────────────────────────────

    private function seedCourses(): void
    {
        $this->command->info("  → Seeding courses...");

        $instructor = Instructor::first();

        $courses = [
            [
                "title" => "Modern Maize Production in Malawi",
                "category" => "soil_crops",
                "access_type" => "free",
                "price" => 0,
                "level" => "beginner",
                "is_featured" => true,
                "total_lessons" => 12,
                "total_duration_minutes" => 360,
                "average_rating" => 4.8,
                "total_enrolled" => 3241,
                "what_you_learn" =>
                    "Land preparation, seed selection, fertilizer application, pest management, harvesting",
            ],
            [
                "title" => "Profitable Dairy Farming",
                "category" => "livestock",
                "access_type" => "paid",
                "price" => 180,
                "level" => "intermediate",
                "is_featured" => true,
                "total_lessons" => 15,
                "total_duration_minutes" => 540,
                "average_rating" => 4.7,
                "total_enrolled" => 1820,
            ],
            [
                "title" => "Drip Irrigation Setup & Management",
                "category" => "irrigation",
                "access_type" => "free",
                "price" => 0,
                "level" => "beginner",
                "is_featured" => true,
                "total_lessons" => 8,
                "total_duration_minutes" => 240,
                "average_rating" => 4.9,
                "total_enrolled" => 2150,
            ],
            [
                "title" => "Organic Vegetable Production",
                "category" => "organic",
                "access_type" => "free",
                "price" => 0,
                "level" => "beginner",
                "is_featured" => true,
                "total_lessons" => 10,
                "total_duration_minutes" => 300,
                "average_rating" => 4.6,
                "total_enrolled" => 1540,
            ],
            [
                "title" => "Agribusiness Finance for Farmers",
                "category" => "agribusiness",
                "access_type" => "paid",
                "price" => 220,
                "level" => "intermediate",
                "is_featured" => true,
                "total_lessons" => 14,
                "total_duration_minutes" => 420,
                "average_rating" => 4.8,
                "total_enrolled" => 980,
            ],
            [
                "title" => "Drone Technology in Modern Farming",
                "category" => "agri_tech",
                "access_type" => "premium",
                "price" => 350,
                "level" => "advanced",
                "is_featured" => true,
                "total_lessons" => 18,
                "total_duration_minutes" => 720,
                "average_rating" => 4.9,
                "total_enrolled" => 542,
            ],
            [
                "title" => "Soybeans Production Guide",
                "category" => "soil_crops",
                "access_type" => "free",
                "price" => 0,
                "level" => "beginner",
                "is_featured" => false,
                "total_lessons" => 9,
                "total_duration_minutes" => 270,
                "average_rating" => 4.5,
                "total_enrolled" => 1230,
            ],
            [
                "title" => "Poultry Farming for Profit",
                "category" => "livestock",
                "access_type" => "paid",
                "price" => 150,
                "level" => "beginner",
                "is_featured" => false,
                "total_lessons" => 12,
                "total_duration_minutes" => 360,
                "average_rating" => 4.4,
                "total_enrolled" => 876,
            ],
            [
                "title" => "Post-Harvest Handling & Storage",
                "category" => "post_harvest",
                "access_type" => "free",
                "price" => 0,
                "level" => "intermediate",
                "is_featured" => false,
                "total_lessons" => 7,
                "total_duration_minutes" => 210,
                "average_rating" => 4.6,
                "total_enrolled" => 1870,
            ],
            [
                "title" => "Climate-Smart Agriculture",
                "category" => "agri_tech",
                "access_type" => "free",
                "price" => 0,
                "level" => "intermediate",
                "is_featured" => false,
                "total_lessons" => 11,
                "total_duration_minutes" => 330,
                "average_rating" => 4.7,
                "total_enrolled" => 1124,
            ],
        ];

        foreach ($courses as $data) {
            Course::firstOrCreate(
                ["title" => $data["title"]],
                array_merge($data, [
                    "instructor_id" => $instructor->id,
                    "description" => "A comprehensive course covering everything you need to know about {$data["title"]}. Perfect for farmers in Malawi and Zambia.",
                    "status" => "published",
                    "currency" => "MWK",
                    "total_reviews" => rand(20, 200),
                    "has_certificate" => true,
                    "works_offline" => false,
                ]),
            );
        }

        $this->command->info("    ✓ " . Course::count() . " courses seeded");
    }

    // ── Products ──────────────────────────────────────────────────────

    private function seedProducts(): void
    {
        $this->command->info("  → Seeding products...");

        $seller = User::farmers()->first();
        if (!$seller) {
            return;
        }

        $products = [
            [
                "name" => "DK8031 Hybrid Maize Seed",
                "category" => "seeds",
                "price" => 4800,
                "unit" => "10kg bag",
                "stock_quantity" => 200,
                "district" => "Lilongwe",
                "description" =>
                    "High-yielding hybrid maize variety, drought-tolerant, gives up to 8 tonnes/ha under proper management.",
                "is_featured" => true,
            ],
            [
                "name" => "NPK 23:21:0+4S Basal Fertilizer",
                "category" => "fertilizer",
                "price" => 28000,
                "unit" => "50kg bag",
                "stock_quantity" => 150,
                "district" => "Blantyre",
                "description" =>
                    "Premium basal dressing fertilizer for maize, tobacco and soybeans. Promotes strong root development.",
                "is_featured" => true,
            ],
            [
                "name" => "Fresh Tomatoes (Grade A)",
                "category" => "produce",
                "price" => 3500,
                "unit" => "crate",
                "stock_quantity" => 80,
                "district" => "Zomba",
                "description" =>
                    "Fresh Roma tomatoes, Grade A quality. Harvested daily. Ideal for restaurants and supermarkets.",
                "is_featured" => true,
            ],
            [
                "name" => "Improved Malawi Goats (Breeding Pair)",
                "category" => "livestock",
                "price" => 125000,
                "unit" => "pair",
                "stock_quantity" => 5,
                "district" => "Mzimba",
                "description" =>
                    "Healthy, vaccinated improved goats ready for breeding. High milk and meat production.",
                "is_featured" => true,
            ],
            [
                "name" => "CAN 27% Nitrogen Top Dressing",
                "category" => "fertilizer",
                "price" => 26000,
                "unit" => "50kg bag",
                "stock_quantity" => 300,
                "district" => "Kasungu",
                "description" =>
                    "Calcium Ammonium Nitrate for top dressing maize and vegetables. Fast-acting nitrogen.",
                "is_featured" => false,
            ],
            [
                "name" => "Treadle Pump Irrigation Kit",
                "category" => "tools",
                "price" => 45000,
                "unit" => "set",
                "stock_quantity" => 20,
                "district" => "Salima",
                "description" =>
                    "Manual treadle pump system for drawing water from shallow wells. No electricity needed.",
                "is_featured" => true,
            ],
            [
                "name" => "Fresh Cabbage (100 heads)",
                "category" => "produce",
                "price" => 8500,
                "unit" => "bag",
                "stock_quantity" => 40,
                "district" => "Dedza",
                "description" =>
                    "Fresh mature cabbages, 100 heads per bag. Ready for market.",
                "is_featured" => false,
            ],
            [
                "name" => "Coragen Insecticide (Fall Armyworm)",
                "category" => "chemicals",
                "price" => 12500,
                "unit" => "200ml bottle",
                "stock_quantity" => 100,
                "district" => "Lilongwe",
                "description" =>
                    "Chlorantraniliprole 200g/L SC. The most effective control for Fall Armyworm in maize.",
                "is_featured" => true,
            ],
            [
                "name" => "Soybean Seed Maluwa Variety",
                "category" => "seeds",
                "price" => 3800,
                "unit" => "kg",
                "stock_quantity" => 500,
                "district" => "Blantyre",
                "description" =>
                    "High-protein soybean seed, Maluwa variety. Disease-resistant, gives 2-3 tonnes/ha.",
                "is_featured" => false,
            ],
            [
                "name" => "Motorized Water Pump 2-inch",
                "category" => "equipment",
                "price" => 185000,
                "unit" => "unit",
                "stock_quantity" => 8,
                "district" => "Lilongwe",
                "description" =>
                    "2-inch petrol-powered water pump for irrigation. 30,000 litres/hour capacity.",
                "is_featured" => false,
            ],
            [
                "name" => "Fresh Groundnuts (Roasted)",
                "category" => "produce",
                "price" => 4200,
                "unit" => "50kg bag",
                "stock_quantity" => 60,
                "district" => "Ntcheu",
                "description" =>
                    "Premium roasted groundnuts, Grade 1. Direct from farm, no additives.",
                "is_featured" => false,
            ],
            [
                "name" => "Dairy Cow (Friesian Cross)",
                "category" => "livestock",
                "price" => 850000,
                "unit" => "head",
                "stock_quantity" => 3,
                "district" => "Mzimba",
                "description" =>
                    "Productive Friesian cross dairy cow, 15-20 litres milk/day. Vaccinated and dewormed.",
                "is_featured" => false,
            ],
        ];

        foreach ($products as $data) {
            Product::firstOrCreate(
                ["name" => $data["name"]],
                array_merge($data, [
                    "seller_id" => $seller->id,
                    "status" => "active",
                    "currency" => "MWK",
                    "minimum_order" => 1,
                    "in_stock" => true,
                    "average_rating" => round(rand(38, 50) / 10, 1),
                    "total_reviews" => rand(3, 45),
                    "total_sold" => rand(5, 200),
                    "total_views" => rand(50, 500),
                    "is_verified" => true,
                    "delivery_available" => true,
                ]),
            );
        }

        $this->command->info("    ✓ " . Product::count() . " products seeded");
    }

    // ── Diseases ──────────────────────────────────────────────────────

    private function seedDiseases(): void
    {
        $this->command->info("  → Seeding disease library...");

        $diseases = [
            [
                "name" => "Fall Armyworm",
                "scientific_name" => "Spodoptera frugiperda",
                "affected_crop" => "Maize",
                "category" => "pest",
                "severity" => "Critical",
                "spread_vector" => "Wind, Adult moths",
                "peak_season" => "November–March",
                "symptoms" => [
                    "Ragged holes in leaves",
                    "Frass (droppings) in maize whorl",
                    "Larvae (caterpillars) visible at night",
                    "Stunted plant growth",
                    "Yellowing of leaves around whorl",
                ],
                "cause" =>
                    "Invasive moth species Spodoptera frugiperda, originally from the Americas, now widespread across sub-Saharan Africa.",
                "spread_mechanism" =>
                    "Adult moths can fly up to 100km in one night. Larvae hatch inside the maize whorl and feed on leaves before moving into the cob.",
                "seasonal_info" =>
                    "Most destructive during the main growing season (Nov–Mar) when maize is at vegetative stage. Peak egg-laying occurs during warm humid nights.",
                "treatment_steps" => [
                    "Scout fields weekly from germination",
                    "If >20% plants infested, apply Coragen (Chlorantraniliprole) 10ml/20L water",
                    "Apply spray directly into the maize whorl, not on leaves",
                    "Apply early morning or evening for maximum efficacy",
                    "Repeat after 14 days if infestation persists",
                    "Collect and destroy larvae by hand for small plots",
                ],
                "prevention_methods" => [
                    "Plant early (October) to avoid peak moth season",
                    "Use push-pull intercropping with Desmodium",
                    "Apply wood ash or sand into whorl as physical barrier",
                    "Use pheromone traps to monitor moth populations",
                    "Plant Bt maize varieties if available",
                ],
                "recommended_products" => [
                    ["name" => "Coragen 200SC", "price" => "K 12,500/200ml"],
                    ["name" => "Ampligo", "price" => "K 8,900/100ml"],
                    ["name" => "Orthene (Acephate)", "price" => "K 4,200/100g"],
                ],
                "impact_stat" => "80% faster AI diagnosis saves crops",
                "is_published" => true,
            ],
            [
                "name" => "Maize Lethal Necrosis (MLN)",
                "scientific_name" => "Maize chlorotic mottle virus + SCMV",
                "affected_crop" => "Maize",
                "category" => "viral",
                "severity" => "Critical",
                "spread_vector" => "Thrips, Aphids",
                "peak_season" => "October–February",
                "symptoms" => [
                    "Yellowing (chlorosis) of leaves from the edges inward",
                    "Necrosis (death) of leaf tissue",
                    "Premature tasseling",
                    "Poor or no grain fill",
                    "Stunted plants",
                    "Mottled streaking on leaves",
                ],
                "cause" =>
                    "Co-infection of two viruses: Maize chlorotic mottle virus (MCMV) and Sugarcane mosaic virus (SCMV). Both must be present for MLN.",
                "spread_mechanism" =>
                    "Transmitted by thrips (MCMV) and aphids (SCMV). Can also spread through infected seed and contaminated equipment.",
                "seasonal_info" =>
                    "Most severe during warm, dry conditions that favor thrips and aphid populations.",
                "treatment_steps" => [
                    "Remove and destroy infected plants immediately — do NOT leave in field",
                    "Apply thiamethoxam-based insecticide to control vector insects",
                    "Plant resistant MLN varieties (e.g. WH507, PH4)",
                    "Do not save seed from infected plants",
                    "Rotate out of maize for at least one season",
                ],
                "prevention_methods" => [
                    "Use certified disease-free seed",
                    "Plant MLN-tolerant varieties",
                    "Control thrips and aphids with approved insecticides",
                    "Remove volunteer maize plants",
                    "Implement a 4-week fallow period between seasons",
                ],
                "recommended_products" => [
                    [
                        "name" => "Actara 25WG (Thiamethoxam)",
                        "price" => "K 6,800/100g",
                    ],
                    [
                        "name" => "Confidor (Imidacloprid)",
                        "price" => "K 5,400/100ml",
                    ],
                ],
                "is_published" => true,
            ],
            [
                "name" => "Late Blight of Potato & Tomato",
                "scientific_name" => "Phytophthora infestans",
                "affected_crop" => "Potato / Tomato",
                "category" => "fungal",
                "severity" => "High",
                "spread_vector" => "Wind, Water splash",
                "peak_season" => "December–February",
                "symptoms" => [
                    "Water-soaked lesions on leaves",
                    "White powdery growth on leaf underside",
                    "Brown/black necrosis spreading rapidly",
                    "Infected tubers show brown rot inside",
                    "Characteristic musty smell from infected plants",
                ],
                "cause" =>
                    "Oomycete pathogen Phytophthora infestans. Thrives in cool (10–25°C), wet conditions.",
                "spread_mechanism" =>
                    "Spores spread rapidly by wind and water splash. Can destroy an entire field within 1 week under favorable conditions.",
                "seasonal_info" =>
                    "Peak risk during rainy season (Dec–Feb) especially at higher altitudes (Dedza, Zomba, Mulanje) where cool moist conditions prevail.",
                "treatment_steps" => [
                    "Apply Ridomil Gold (Metalaxyl + Mancozeb) at first sign of infection",
                    "Spray on a 7-day interval during wet weather",
                    "Remove and destroy infected plant material",
                    "Improve drainage in the field",
                    "Apply copper-based fungicide as preventative during high-risk periods",
                ],
                "prevention_methods" => [
                    "Use certified blight-resistant varieties",
                    "Plant in well-drained soils",
                    "Avoid overhead irrigation — water at base of plants",
                    "Maintain good air circulation between plants",
                    "Monitor weather forecasts — spray before rain",
                ],
                "recommended_products" => [
                    ["name" => "Ridomil Gold MZ", "price" => "K 14,200/1kg"],
                    [
                        "name" => "Dithane M45 (Mancozeb)",
                        "price" => "K 5,800/1kg",
                    ],
                    [
                        "name" => "Bravo (Chlorothalonil)",
                        "price" => "K 7,500/1L",
                    ],
                ],
                "is_published" => true,
            ],
            [
                "name" => "Cassava Mosaic Disease (CMD)",
                "scientific_name" => "African cassava mosaic virus",
                "affected_crop" => "Cassava",
                "category" => "viral",
                "severity" => "High",
                "spread_vector" => "Whitefly (Bemisia tabaci)",
                "peak_season" => "Year-round, peaks Nov–Feb",
                "symptoms" => [
                    "Mosaic pattern (yellow/green patches) on leaves",
                    "Leaf distortion and curling",
                    "Reduced leaf size",
                    "Stunted plant growth",
                    "Reduced root yield by 20–90%",
                ],
                "cause" =>
                    "African cassava mosaic virus (ACMV) transmitted by the whitefly Bemisia tabaci.",
                "spread_mechanism" =>
                    "Primarily through infected planting material (stem cuttings). Also spread by whitefly vectors from plant to plant.",
                "seasonal_info" =>
                    "Present year-round but new infections peak when whitefly populations are highest in hot, dry conditions.",
                "treatment_steps" => [
                    "Remove and destroy infected plants from field",
                    "Only use CMD-free certified planting material",
                    "Control whitefly populations with insecticides",
                    "Plant CMD-resistant varieties (Mbundumali, Sauti)",
                    "Rogue out severely infected plants immediately",
                ],
                "prevention_methods" => [
                    "Source planting material from disease-free gardens",
                    "Plant CMD-resistant improved varieties",
                    "Control whitefly vectors regularly",
                    "Maintain field hygiene — remove crop debris",
                    "Establish cassava away from existing infected fields",
                ],
                "recommended_products" => [
                    [
                        "name" => "Confidor (Imidacloprid)",
                        "price" => "K 5,400/100ml",
                    ],
                    [
                        "name" => "Karate (Lambda-cyhalothrin)",
                        "price" => "K 3,800/100ml",
                    ],
                ],
                "is_published" => true,
            ],
            [
                "name" => "Groundnut Rosette Disease",
                "scientific_name" => "Groundnut rosette virus (GRV)",
                "affected_crop" => "Groundnuts",
                "category" => "viral",
                "severity" => "High",
                "spread_vector" => "Aphid (Aphis craccivora)",
                "peak_season" => "November–February",
                "symptoms" => [
                    "Severe stunting of plants",
                    "Chlorotic rosette — yellow-green mottled leaves",
                    'Small, bunched leaves giving a "mosaic" appearance',
                    "Very few or no pods formed",
                    "Aphid colonies visible on stems and leaves",
                ],
                "cause" =>
                    "Groundnut rosette virus (GRV) transmitted by groundnut aphid Aphis craccivora.",
                "spread_mechanism" =>
                    "Spread rapidly by winged aphids from infected wild hosts and previously infected groundnut plants.",
                "seasonal_info" =>
                    "Most severe at high aphid pressure periods (dry spells during rainy season). Early-planted groundnuts are least affected.",
                "treatment_steps" => [
                    "Spray with imidacloprid or thiamethoxam to control aphids",
                    "Remove and destroy infected plants",
                    "Apply seed dressing (imidacloprid) at planting to protect early seedlings",
                    "Plant resistant varieties (RG1, ICGV86031)",
                ],
                "prevention_methods" => [
                    "Plant early (November) to avoid peak aphid season",
                    "Use imidacloprid seed treatment before planting",
                    "Maintain recommended plant spacing to reduce spread",
                    "Plant rosette-resistant varieties",
                    "Monitor fields weekly for aphid colonies",
                ],
                "recommended_products" => [
                    [
                        "name" => "Gaucho (Imidacloprid seed treatment)",
                        "price" => "K 8,500/250g",
                    ],
                    ["name" => "Actara 25WG", "price" => "K 6,800/100g"],
                ],
                "is_published" => true,
            ],
            [
                "name" => "Bacterial Wilt of Solanaceae",
                "scientific_name" => "Ralstonia solanacearum",
                "affected_crop" => "Tomato / Potato / Pepper",
                "category" => "bacterial",
                "severity" => "High",
                "spread_vector" => "Soil, Water, Infected tools",
                "peak_season" => "December–March (hot, wet)",
                "symptoms" => [
                    "Sudden wilting of whole plant even when soil is moist",
                    "Brown discolouration of stem vascular tissue inside",
                    "Bacterial ooze (white slime) from cut stems when dipped in water",
                    "Lower leaves wilt first, spreading upward",
                    "Plants die within days of symptom appearance",
                ],
                "cause" =>
                    "Soil-borne bacterium Ralstonia solanacearum. Survives in soil for many years and enters through root wounds.",
                "spread_mechanism" =>
                    "Spreads through infected soil, irrigation water, contaminated tools, and infected transplants.",
                "seasonal_info" =>
                    "Worst during hot, humid conditions. Poorly-drained soils with high soil temperature favor spread.",
                "treatment_steps" => [
                    "Remove and destroy infected plants — do NOT compost",
                    "There is no chemical cure — prevention is essential",
                    "Solarize soil (clear plastic for 4–6 weeks in dry season) to reduce bacterial load",
                    "Apply lime to adjust soil pH to 6.5–7.0",
                    "Drench remaining plants with copper hydroxide as suppressive measure",
                ],
                "prevention_methods" => [
                    "Use grafted tomatoes on wilt-resistant rootstocks",
                    "Avoid overhead irrigation",
                    "Rotate with non-solanaceous crops for 3+ years",
                    "Disinfect tools with bleach between plants",
                    "Use raised beds with good drainage",
                ],
                "recommended_products" => [
                    [
                        "name" => "Kocide (Copper hydroxide)",
                        "price" => "K 11,200/1kg",
                    ],
                    [
                        "name" => "Phyton (Copper sulfate)",
                        "price" => "K 8,800/1L",
                    ],
                ],
                "is_published" => true,
            ],
            [
                "name" => "Maize Streak Virus (MSV)",
                "scientific_name" => "Maize streak virus",
                "affected_crop" => "Maize",
                "category" => "viral",
                "severity" => "Medium",
                "spread_vector" => "Leafhopper (Cicadulina mbila)",
                "peak_season" => "November–January",
                "symptoms" => [
                    "Narrow, pale yellow streaks along leaf veins",
                    "Streaks run parallel to leaf midrib",
                    "Yellowing and stunting of young plants",
                    "Reduced cob size and grain fill",
                    "Leafhoppers visible on leaves",
                ],
                "cause" =>
                    "Maize streak virus (MSV) transmitted by the leafhopper Cicadulina mbila.",
                "spread_mechanism" =>
                    "Leafhoppers acquire the virus from infected maize or wild grass hosts and transmit it when feeding on healthy plants.",
                "seasonal_info" =>
                    "Early-season infections (November) cause the most damage. Infections after tasseling cause minimal yield loss.",
                "treatment_steps" => [
                    "Apply lambda-cyhalothrin or imidacloprid to control leafhoppers",
                    "Rogue out severely infected young plants",
                    "Ensure timely planting to minimize infection window",
                    "Apply insecticide at emergence to protect seedlings",
                ],
                "prevention_methods" => [
                    "Plant MSV-tolerant varieties",
                    "Plant early to escape peak leafhopper season",
                    "Apply imidacloprid seed dressing",
                    "Control grass weeds around fields (alternate leafhopper hosts)",
                    "Monitor for leafhopper presence from emergence",
                ],
                "recommended_products" => [
                    [
                        "name" => "Karate (Lambda-cyhalothrin)",
                        "price" => "K 3,800/100ml",
                    ],
                    ["name" => "Actara 25WG", "price" => "K 6,800/100g"],
                ],
                "is_published" => true,
            ],
            [
                "name" => "Gray Leaf Spot",
                "scientific_name" => "Cercospora zeae-maydis",
                "affected_crop" => "Maize",
                "category" => "fungal",
                "severity" => "Medium",
                "spread_vector" => "Wind, Rain splash",
                "peak_season" => "January–March",
                "symptoms" => [
                    "Rectangular gray/tan lesions between leaf veins",
                    "Lesions run parallel to leaf veins",
                    "Grayish fungal growth on lesion surface in humid conditions",
                    "Leaves turn brown and die prematurely",
                    "Reduced photosynthesis and grain fill",
                ],
                "cause" =>
                    "Fungal pathogen Cercospora zeae-maydis. Favored by high humidity and warm temperatures.",
                "spread_mechanism" =>
                    "Spores spread by wind and rain splash. Survives in infected crop residue from previous season.",
                "seasonal_info" =>
                    "Most severe during late season (Jan–Mar) under humid, rainy conditions. Highland areas at higher risk.",
                "treatment_steps" => [
                    "Apply Amistar (Azoxystrobin) or Ortiva at first sign",
                    "Spray entire canopy thoroughly",
                    "Repeat after 14 days under high disease pressure",
                    "Remove infected crop residue after harvest",
                    "Plant resistant varieties in the following season",
                ],
                "prevention_methods" => [
                    "Use GLS-resistant maize varieties",
                    "Rotate maize with soybeans or groundnuts",
                    "Remove and plow under maize residue after harvest",
                    "Avoid very high plant densities",
                    "Ensure adequate plant nutrition (especially potassium)",
                ],
                "recommended_products" => [
                    [
                        "name" => "Amistar (Azoxystrobin)",
                        "price" => "K 18,500/1L",
                    ],
                    [
                        "name" => "Ortiva (Azoxystrobin)",
                        "price" => "K 16,200/1L",
                    ],
                ],
                "is_published" => true,
            ],
        ];

        foreach ($diseases as $data) {
            Disease::firstOrCreate(
                ["name" => $data["name"]],
                array_merge($data, ["view_count" => rand(100, 2000)]),
            );
        }

        $this->command->info("    ✓ " . Disease::count() . " diseases seeded");
    }

    // ── Disease Alerts ────────────────────────────────────────────────

    private function seedDiseaseAlerts(): void
    {
        $this->command->info("  → Seeding disease alerts...");

        $admin = User::where("role", "admin")->first();
        if (!$admin) {
            return;
        }

        $disease = Disease::where("name", "Fall Armyworm")->first();

        DiseaseAlert::firstOrCreate(
            ["title" => "Fall Armyworm Outbreak — Central Region"],
            [
                "disease_id" => $disease?->id,
                "description" =>
                    "Significant Fall Armyworm infestation has been detected across multiple districts in the Central Region. Early intervention is critical to prevent widespread crop losses.",
                "alert_type" => "critical",
                "affected_districts" => [
                    "Lilongwe",
                    "Kasungu",
                    "Dedza",
                    "Mchinji",
                    "Ntchisi",
                    "Dowa",
                ],
                "affected_crops" => ["Maize"],
                "recommended_action" =>
                    "Scout fields immediately. Apply Coragen or Ampligo into maize whorl if >20% plants infested.",
                "source" => "Malawi Ministry of Agriculture",
                "farmers_notified" => 4200,
                "is_active" => true,
                "created_by" => $admin->id,
            ],
        );

        $this->command->info("    ✓ Disease alerts seeded");
    }

    // ── Innovations ───────────────────────────────────────────────────

    private function seedInnovations(): void
    {
        $this->command->info("  → Seeding innovations...");

        $farmer = User::farmers()->first();
        if (!$farmer) {
            return;
        }

        $innovations = [
            [
                "title" => "Low-Cost Rainwater Harvesting System",
                "category" => "water_management",
                "description" =>
                    "A simple system using locally-available materials (buckets, pipes, gutters) to collect rainwater from rooftops into underground storage tanks. The stored water is then used for drip irrigation during dry spells, reducing water use by 60% compared to conventional methods.",
                "impact_summary" => "60% less water used per season",
                "estimated_cost" => "K 8,500 for 1 acre setup",
                "district" => "Lilongwe",
                "vote_count" => 342,
                "view_count" => 1240,
                "status" => "approved",
            ],
            [
                "title" => "Solar-Powered Crop Pest Monitor",
                "category" => "technology",
                "description" =>
                    "Built using a solar panel, a Raspberry Pi and a camera to automatically detect and count Fall Armyworm moths in pheromone traps. Sends SMS alert to farmer when moth count exceeds threshold. Costs K 45,000 to build and lasts 5+ years.",
                "impact_summary" => "3 days earlier pest warning",
                "estimated_cost" => "K 45,000 total build cost",
                "district" => "Mzimba",
                "vote_count" => 287,
                "view_count" => 985,
                "status" => "approved",
            ],
            [
                "title" => "Crop Residue Biochar for Soil Improvement",
                "category" => "crop_solutions",
                "description" =>
                    "Converting maize stalks and groundnut shells into biochar through slow burning in a homemade kiln. Adding biochar to soil at 2 tonnes/ha improved water retention, increased pH from 5.2 to 6.1, and boosted maize yield by 45% over two seasons.",
                "impact_summary" => "45% yield increase over 2 seasons",
                "estimated_cost" => "K 1,200 per hectare",
                "district" => "Zomba",
                "vote_count" => 198,
                "view_count" => 760,
                "status" => "approved",
            ],
        ];

        foreach ($innovations as $data) {
            Innovation::firstOrCreate(
                ["title" => $data["title"]],
                array_merge($data, [
                    "user_id" => $farmer->id,
                    "slug" => \Illuminate\Support\Str::slug($data["title"]),
                    "in_competition" => false,
                ]),
            );
        }

        $this->command->info(
            "    ✓ " . Innovation::count() . " innovations seeded",
        );
    }

    // ── Competition ───────────────────────────────────────────────────

    private function seedCompetition(): void
    {
        $this->command->info("  → Seeding active competition...");

        Competition::firstOrCreate(
            ["title" => "AgriTech Innovation Award 2026"],
            [
                "description" =>
                    "Submit your best farming innovation for a chance to win K 50,000 in prizes! We are looking for practical, affordable solutions that smallholder farmers in Malawi and Zambia can adopt immediately.",
                "rules" =>
                    "Innovation must be original, practical and affordable. Farmer must have implemented it on their own farm. Maximum 5 photos. No plagiarism.",
                "status" => "active",
                "first_prize" => 25000,
                "second_prize" => 15000,
                "third_prize" => 10000,
                "entry_fee" => 0,
                "starts_at" => now()->subDays(15),
                "ends_at" => now()->addDays(45),
                "max_entries" => 200,
                "entry_count" => 47,
            ],
        );

        $this->command->info("    ✓ Competition seeded");
    }
}

<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Farm;
use App\Models\Instructor;
use App\Models\Course;
use App\Models\Lesson;
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
                    "description" => "A comprehensive course covering everything you need to know about {$data["title"]}. Perfect for farmers in Malawi.",
                    "status" => "published",
                    "currency" => "MWK",
                    "total_reviews" => rand(20, 200),
                    "has_certificate" => true,
                    "works_offline" => false,
                ]),
            );
        }

        $this->seedLessons();

        $this->command->info("    ✓ " . Course::count() . " courses seeded");
    }

    // ── Lessons ───────────────────────────────────────────────────────

    private function seedLessons(): void
    {
        $this->command->info("  → Seeding lessons...");

        \App\Models\LessonProgress::query()->delete();
        \App\Models\Lesson::query()->delete();

        $lessonData = [
            "Modern Maize Production in Malawi" => [
                ["title" => "Introduction to Maize Farming in Malawi", "type" => "text", "description" => "Overview of maize as Malawi's staple crop. Maize accounts for over 60% of caloric intake. Learn about the major growing regions: Central (Lilongwe, Kasungu, Ntchisi), Southern (Thyolo, Mulanje, Chiradzulu), and Northern (Mzimba, Rumphi). Understanding the maize calendar: planting in November-December, harvesting in April-May.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Land Preparation & Soil Testing", "type" => "video", "description" => "How to prepare land for maize in Malawi using both manual (hoe) and ox-plough methods. Soil testing kits available from Agricultural Development Division (ADD) offices. Understanding soil pH requirements (5.5-7.0 for maize). Timing of land preparation: early ploughing after first rains in October-November.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Seed Selection & Planting", "type" => "video", "description" => "Choosing the right maize varieties for Malawi: DK8031, MH18, ZS242, and OPV varieties like Kalima. Seed rates: 20-25 kg/ha for hybrids, 10-12 kg/ha for OPVs. Planting depth: 3-5cm. Spacing: 75cm between rows, 25cm between plants. Planting in pairs and thinning to one plant per station.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Fertilizer Application (Basal & Top Dressing)", "type" => "video", "description" => "Understanding fertilizer types available in Malawi: NPK 23:21:0+4S (basal), CAN 27% N (top dressing), Urea 46% N. Application rates: 1 bag NPK per acre at planting, 1 bag CAN per acre at 4-6 weeks after emergence. Micro-dosing techniques for resource-poor farmers. Using the Malawi Fertilizer Program guidelines.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Pest Management: Fall Armyworm Control", "type" => "video", "description" => "Identifying Fall Armyworm damage: holes in leaves, frass (sawdust-like droppings) in leaf whorl. Scouting methods: walk fields weekly from December. Chemical controls: Coragen (chlorantraniliprole), Ampligo, Orthene. Biological controls: Tephritid fruit fly parasitoids. Integrated Pest Management (IPM) approach recommended by Malawi's MoA.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Disease Management: Maize Lethal Necrosis & Streak Virus", "type" => "video", "description" => "Identifying Maize Lethal Necrosis (MLN): yellowing, premature drying. Maize Streak Virus: chlorotic streaks on leaves, transmitted by leafhoppers. Prevention: use certified seed, remove infected plants, rotate crops. Resistance varieties available from CIMMYT research. When to spray fungicides.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Weed Management Strategies", "type" => "text", "description" => "Critical weeding periods: first weeding at 3-4 weeks after planting, second at 6-8 weeks. Manual hoe weeding vs. pre-emergence herbicides (Dual Gold, Callisto). Weed competition can reduce yields by 50-80%. Conservation agriculture techniques: mulching with crop residues, minimum tillage. Weed management in rotation crops.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Water Management & Rainfed Farming", "type" => "text", "description" => "Most maize in Malawi is rainfed. Understanding rainfall patterns: Kasungu (800-1000mm), Lilongwe (750-900mm), Mulanje (1200-1500mm). Conservation agriculture for moisture retention. Supplemental irrigation options: bucket drip kits, small pumps from Shire River. Identifying drought stress: leaf rolling, early maturity.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Harvesting & Post-Harvest Handling", "type" => "video", "description" => "Knowing when to harvest: husks turn brown, grains hard, milk stage ends. Harvesting methods: manual shelling vs. mechanical shellers available at ADMARC. Moisture content: harvest at 20-25%, dry to 12-13% for storage. Drying on concrete floors or tarpaulins. Avoiding aflatoxin contamination: dry quickly, store dry.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Storage & Aflatoxin Prevention", "type" => "video", "description" => "Storage options: hermetic bags (PICS bags), metal silos, community warehouses. Aflatoxin is a major concern in Malawi: caused by Aspergillus fungi in warm, moist conditions. Prevention: proper drying, hermetic storage, avoiding cracked grains. Testing aflatoxin levels: use AflaCheck kits. ADMARC storage standards.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Marketing Your Maize: ADMARC & Private Buyers", "type" => "text", "description" => "Selling options in Malawi: ADMARC (government guaranteed price, currently ~MWK 350/kg), private traders, local markets (mafurao). Contract farming with Ethanol Company (Illovo) and other buyers. Negotiating prices. Using AgriTech Pro marketplace to reach buyers directly. Documentation needed: national ID, weighbridge tickets.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Next Steps", "type" => "text", "description" => "Review of key concepts: certified seed, proper spacing, timely fertilizer, IPM for Fall Armyworm, proper storage. Next steps: join farmer groups (VSLA), access Affordable Inputs Program (AIP), apply for ADMARC loans. Recommended resources: MoA Extension Officers, NAFAKA program, AgriTech Pro community forums.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Profitable Dairy Farming" => [
                ["title" => "Dairy Farming Overview in Malawi", "type" => "text", "description" => "The dairy industry in Malawi: major players include Kal Dairy, Suncrest Creameries, and smallholder cooperatives. Milk demand exceeds supply. Average herd size: 2-5 cows for smallholders. Potential earnings: MWK 3,000-5,000/cow/day. Key breeds: Holstein-Friesian, Jersey, Friesian x Jersey crosses, Malawi Zebu (improved).", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Breed Selection & Housing", "type" => "video", "description" => "Choosing breeds for Malawi conditions: Holstein-Friesian (high yield, needs good management), Jersey (hardy, efficient feed conversion), Crosses (best balance). Housing requirements: ventilated, raised floor, drainage, milking area. Construction using local materials: brick, timber, iron sheets. Space: 1.5m x 2.5m per cow.", "duration_minutes" => 35, "is_free_preview" => true],
                ["title" => "Feed & Nutrition Management", "type" => "video", "description" => "Feed requirements for dairy cows in Malawi: Napier grass (main roughage), maize bran, soybean meal, commercial dairy meal. Daily ration: 15-20kg Napier grass + 3-5kg concentrates per cow. Growing Napier grass: plant cuttings 60cm apart, harvest at 60-90cm height. Calliandra and Leucaena as protein supplements.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Milk Production & Hygiene", "type" => "video", "description" => "Milking procedures: clean udders with warm water, massage, milk completely. Hygiene critical: wash hands, clean equipment, filter milk. Milking frequency: 2-3 times daily. Milk handling: cool to 4°C within 2 hours. Storage: stainless steel containers. Testing: lactometer, alcohol test. Delivering to collection centers.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Reproduction & Breeding", "type" => "video", "description" => "Heat detection: standing heat lasts 12-18 hours, restlessness, mucus discharge. Artificial Insemination (AI) services available from LITC (Livestock Identification and Trading Company) and private vets. Service cost: MWK 5,000-15,000 per insemination. Gestation period: 283 days. Calving management and calf rearing.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Disease Prevention & Treatment", "type" => "video", "description" => "Common dairy diseases in Malawi: Mastitis (udder infection), Brucellosis, Foot-and-Mouth Disease, East Coast Fever. Vaccination schedule: FMD every 6 months, Brucellosis (S19 vaccine for heifers). Signs of mastitis: clots in milk, swollen udder. Treatment: intramammary antibiotics. Prevention: regular milking, hygiene.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Dairy Economics & Record Keeping", "type" => "text", "description" => "Cost-benefit analysis: initial investment MWK 500,000-1,500,000 for 2 cows. Monthly costs: feed MWK 90,000-150,000/cow. Revenue: 15-25 litres/cow/day x MWK 500-700/litre. Break-even: 12-18 months. Record keeping: milk yield, breeding dates, health treatments, expenses. Using simple notebooks or AgriTech Pro app.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Dairy Value Chains in Malawi", "type" => "text", "description" => "Marketing channels: Suncrest Creameries (contract), Kal Dairy, local markets, school feeding programs. Value addition: ghee, yoghurt, cheese. Setting up a mini processing unit: requirements, licenses (MBFCA), equipment. Cooperative formation: benefits, registration process with Ministry of Trade.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Waste Management & Biogas", "type" => "text", "description" => "Cow dung utilization: biogas production (floating dome digesters cost MWK 200,000-500,000), composting, fuel briquettes. Biogas benefits: free cooking fuel, reduced deforestation, organic fertilizer byproduct (bioslurry). Government incentives for biogas: Malawi Biogas Programme. Setting up a simple digester.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Accessing Finance for Dairy Expansion", "type" => "text", "description" => "Financial options: NBS Bank Agricultural Loans, FDH Bank, Microfinance institutions (FINCA, Opportunity Bank). Government programs: Agricultural Development and Marketing Corporation (ADMARC) loans, Youth Enterprise Development Fund. Collateral requirements: land title, livestock. Business plan templates for dairy farming.", "duration_minutes" => 25, "is_free_preview" => false],
            ],

            "Drip Irrigation Setup & Management" => [
                ["title" => "Why Irrigation Matters in Malawi", "type" => "text", "description" => "Malawi receives erratic rainfall (600-1400mm depending on region). Climate change worsening dry spells in December-January (critical maize flowering period). Irrigation can increase yields 2-4x. Only 3% of Malawi's irrigable land (567,000 ha along Shire River and lakes) is irrigated. Government target: food security through irrigation.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Types of Irrigation Systems", "type" => "video", "description" => "Overview: flood/furrow (traditional, 40-50% efficient), sprinkler (60-70% efficient), drip irrigation (90-95% efficient). Drip irrigation best for Malawi's smallholders: saves 40-60% water, reduces weed pressure, prevents soil erosion. Components: header tank, mainline, sub-mains, laterals, emitters/drippers.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "System Design & Layout", "type" => "video", "description" => "Designing a drip system for a 50m x 30m plot (0.15 ha). Materials needed: 1 x 200L header tank on 2m stand, 32mm HDPE mainline, 16mm laterals with 2L/hr drippers at 30cm spacing. Row spacing: 75cm for vegetables, 90cm for maize. Cost estimate: MWK 35,000-60,000 for 0.15ha. Tools: pipe cutter, punch, tape measure.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Installation Step-by-Step", "type" => "video", "description" => "Step 1: Position header tank (highest point, 2m above ground). Step 2: Lay mainline from tank down the field. Step 3: Connect sub-mains at 90°. Step 4: Attach laterals with grommets. Step 5: Install drippers/emitters. Step 6: Flush system before first use. Step 7: Check for leaks and uniformity. Common mistakes and troubleshooting.", "duration_minutes" => 40, "is_free_preview" => false],
                ["title" => "Water Source Options", "type" => "text", "description" => "Options for Malawi: rainwater harvesting (roof catchment, 1mm rain = 1 litre/m² roof), borehole with hand pump or solar pump, river/stream pumping (Shire River, Bua River, Ruo River), community water points. Solar pump systems: MWK 200,000-800,000 depending on head and flow. Rain tanks: Ferro-cement tanks MWK 50,000-100,000.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Crop Scheduling & Water Management", "type" => "video", "description" => "Water requirements: tomatoes 4-6L/plant/day, onions 2-3L, cabbage 3-5L, maize 3-5L. Scheduling: run system 1-2 hours daily depending on crop stage. Monitoring: feel method (soil squeeze test), tensiometer. Adjusting for Malawi seasons: increase during hot months (Oct-Dec), reduce during cool season (Jun-Aug).", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "System Maintenance & Repair", "type" => "video", "description" => "Daily: check tank level, pressure. Weekly: inspect emitters for clogging. Monthly: flush lines, clean filters. Seasonal: drain before cold season (June-July in highlands). Common problems: clogged drippers (use vinegar/acid cleaning), leaking joints (replace grommets), low pressure (check tank height). Spare parts to keep: extra drippers, grommets, end caps.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Fertigation: Feeding Through Drip Lines", "type" => "text", "description" => "Fertigation: applying soluble fertilizer through drip system. Compatible fertilizers: calcium nitrate, potassium nitrate, NPK water-soluble. Mixing: dissolve in桶 (20L bucket), inject throughVenturi injector or drip directly into tank. Benefits: 30% less fertilizer needed, uniform distribution, saves labor. Schedule: every 2 weeks during growing season.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Economics & Funding Sources", "type" => "text", "description" => "Cost-benefit: drip irrigation increases vegetable income 3-5x. Payback period: 6-12 months for high-value crops. Funding: World Bank IFAD project, Government of Malawi Irrigation Project, NGO programs (CARE, World Vision, Catholic Development Commission). Self-funding through farmer group savings (VSLA). Starting small and scaling up.", "duration_minutes" => 25, "is_free_preview" => false],
            ],

            "Organic Vegetable Production" => [
                ["title" => "Introduction to Organic Farming in Malawi", "type" => "text", "description" => "Organic farming principles: no synthetic chemicals, build soil health, biodiversity. Malawi context: most smallholders are already 'organic by default'. Benefits: lower input costs, premium prices at supermarkets and export markets. Certification: Malawi Organic Agriculture Movement (MOAM), GlobalG.A.P., USDA Organic.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Soil Building & Composting", "type" => "video", "description" => "Composting methods for Malawi: heap method (easiest), pit method (for dry areas), vermicomposting (using African nightcrawlers). Ingredients: green (fresh leaves, kitchen waste) + brown (dry leaves, maize stalks). C:N ratio: 25-30:1. Compost recipe: layer 15cm greens + 10cm browns + 2cm soil. Turn every 2 weeks. Ready in 8-12 weeks.", "duration_minutes" => 35, "is_free_preview" => true],
                ["title" => "Organic Pest & Disease Control", "type" => "video", "description" => "Neem spray: crush 500g neem leaves in 5L water, strain, spray. Chili-garlic spray: blend 200g chili + 100g garlic in 1L water. Ash dusting for aphids. Companion planting: marigold (repels nematodes), basil (repels aphids), tephrosia (fish bean as green manure + pest deterrent). Crop rotation for disease break.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Growing Tomatoes Organically", "type" => "video", "description" => "Variety selection: Roma VF, Money Maker, Cal-J. Raising seedlings: nursery bed, potting mix of compost + soil. Transplanting at 6 weeks. Spacing: 60cm x 100cm. Staking and pruning. Organic fertilization: compost tea every 2 weeks, bone meal at planting. Harvesting: every 3-4 days during peak season. Yield: 20-40 tonnes/ha.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Growing Indigenous Vegetables (Masamba)", "type" => "video", "description" => "Popular indigenous vegetables in Malawi: chambu (amaranthus), khutsiya (rape), chimphamba (sweet potato leaves), nyemba (pigeon pea leaves). High in vitamins A, C, iron. Growing conditions: partial shade to full sun, regular watering. Planting: direct seed or transplant. Harvesting: continuous leaf picking, plant regrows. Market demand: high in urban areas.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Cabbage & Leafy Greens Production", "type" => "video", "description" => "Cabbage varieties: Granat, Cairo, Roundhead. Seedbed preparation, transplanting at 4-5 weeks. Spacing: 45cm x 60cm. Organic pest control: cabbage looper (Bacillus thuringiensis), diamondback moth (neem spray). Growing kale (sukuma wiki): succession planting every 2 weeks for continuous harvest.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Onion & Garlic Production", "type" => "text", "description" => "Onion varieties: Red Creole, Texas Grano. Seedbed: fine tilth, well-drained. Transplanting at 6-8 weeks when pencil-thick. Spacing: 15cm x 30cm. Bulbing stage: reduce watering. Harvesting: when 50% tops fall. Curing: dry in shade for 2 weeks. Garlic: similar management, longer growing period (8-9 months). Market prices: MWK 2,000-4,000/kg.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Organic Certification & Market Access", "type" => "text", "description" => "Getting certified: MOAM inspection process, documentation requirements. Costs: MWK 50,000-150,000 for smallholder groups. Markets: Shoprite, Game, urban organic shops, export (EU, US). Premium prices: 20-50% above conventional. Contract farming opportunities. Using AgriTech Pro to connect with organic buyers.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Packaging & Post-Harvest for Organic Produce", "type" => "text", "description" => "Packaging standards: clean crates, perforated bags for leafy vegetables. Cooling: shade cooling, evaporative cooling (zeer pots). Transport: avoid overcrowding, early morning harvest. Shelf life extension: hydrocooling, ice packing for tomatoes. Organic handling: no chemical treatments, proper washing with clean water.", "duration_minutes" => 20, "is_free_preview" => false],
                ["title" => "Course Summary & Organic Farm Business Plan", "type" => "text", "description" => "Review key concepts. Creating an organic farm business plan: market analysis, production plan, financial projections. Record keeping for organic compliance. Building a farming brand. Resources: MOAM, Organic Trade Association, AgriTech Pro organic community.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Agribusiness Finance for Farmers" => [
                ["title" => "Financial Literacy Basics", "type" => "text", "description" => "Understanding money management for farming. Key concepts: income, expenses, profit, cash flow. The difference between profit and cash flow (critical for seasonal farming). Setting financial goals: short-term (this season), medium-term (1-3 years), long-term (5+ years). Budgeting for the farming season.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Farm Record Keeping", "type" => "video", "description" => "Essential records: field records (planting dates, inputs used, yields), financial records (income, expenses, loans), livestock records (births, deaths, treatments, production). Simple bookkeeping methods: notebook system, envelope method. Digital tools: AgriTech Pro app, M-Pesa statements. Why records matter: access to credit, track profitability, tax compliance.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Farm Budgeting & Planning", "type" => "video", "description" => "Creating a seasonal farm budget: estimate all costs (seed, fertilizer, labor, transport) before the season. Estimating income: realistic yields x expected prices. Cash flow planning: when expenses are needed vs. when income arrives. Contingency planning: 10-15% buffer for unexpected costs. Example budget: 1 hectare maize in Malawi.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Understanding Credit & Loans", "type" => "text", "description" => "Types of credit in Malawi: formal (banks, microfinance), semi-formal (savings groups), informal (family, traders). Interest rates: banks 20-35%, microfinance 30-50%, VSLA 0% (savings). Loan requirements: business plan, collateral, national ID. Understanding APR, loan terms, repayment schedules. Red flags: predatory lenders, hidden fees.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Accessing Agricultural Credit", "type" => "text", "description" => "Financial institutions: NBS Bank, FDH Bank, CDH Investment Bank, First Capital Bank. Microfinance: FINCA Malawi, Opportunity Bank, EDFIN. Government programs: Agricultural Loan Board, Affordable Inputs Program (AIP). Requirements: registration certificate, tax pin, business plan, collateral. Tips for successful applications.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Savings Groups & VSLAs", "type" => "video", "description" => "Village Savings and Loan Associations (VSLAs): how they work. Formation: 15-25 members, regular meetings (weekly/biweekly). Savings collection, loan disbursement, interest earned. Benefits: no collateral needed, builds financial discipline, social capital. Starting a VSLA: step-by-step guide using CARE methodology.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Market Analysis & Pricing Strategies", "type" => "text", "description" => "Understanding market dynamics in Malawi: seasonal price fluctuations (low prices Feb-May during harvest, high prices Aug-Nov). Price data sources: ADMARC published prices, FISP reports, AgriTech Pro market data. Pricing strategy: cost-plus pricing, market-based pricing. Finding the best market: local market vs. urban vs. exporter.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Value Addition & Processing", "type" => "text", "description" => "Adding value to raw agricultural products: drying (tomatoes, fish, fruits), milling (maize to flour), pressing (soybean to oil), packaging. Examples in Malawi: groundnut paste, dried mango, cassava flour. Equipment costs, licensing (MBFCA), food safety requirements. Value addition can increase income 2-5x.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Insurance & Risk Management", "type" => "text", "description" => "Farm risk management: diversification, crop insurance (available through some banks with loans), savings buffer. Weather risks in Malawi: drought, flooding, delayed rains. Insurance options: ILRI index-based weather insurance pilots. Contract farming as risk mitigation. Government relief programs: FISP, disaster response.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Tax Obligations for Farmers", "type" => "text", "description" => "Tax requirements in Malawi: Turnover Tax (1.5% for turnover under MWK 10M), Income Tax (25-35% above MWK 1.5M threshold). Registering with MRA (Malawi Revenue Authority): get a Taxpayer Identification Number (TPIN). Filing returns: July 31 annually. Exemptions: subsistence farmers below threshold. Keeping tax records.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Business Registration & Formalization", "type" => "text", "description" => "Why formalize: access to markets, credit, government programs. Registration options: sole proprietorship, cooperative, limited company. Process: Registrar General's Office, BLAST (Business Licence Allocation System). Cooperatives: register with Cooperatives Commission, benefits include bulk buying, group marketing. Costs: MWK 10,000-50,000.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Writing a Business Plan", "type" => "video", "description" => "Business plan structure: Executive Summary, Market Analysis, Production Plan, Marketing Plan, Financial Projections, Risk Analysis. Template: use Agricultural Development Bank (ADB) or KfW templates. Example: 5-hectare mixed farming business plan. Financial projections: 3-year income/expense forecast. Presenting to banks: tips for loan applications.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Success Stories: Malawian Farmer Entrepreneurs", "type" => "text", "description" => "Case studies: From subsistence to commercial farming in Kasungu, Dairy cooperative success in Mangochi, Organic vegetable exporter in Lilongwe. Lessons learned: start small, scale gradually, keep records, access training. Inspirational stories to motivate your farming business journey.", "duration_minutes" => 20, "is_free_preview" => false],
                ["title" => "Course Summary & Action Plan", "type" => "text", "description" => "Key takeaways: financial literacy, record keeping, credit access, market knowledge. Create your personal action plan: 3 financial goals for this season. Resources: MRA, financial institutions, AgriTech Pro finance tools, Ministry of Agriculture extension services.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Drone Technology in Modern Farming" => [
                ["title" => "Introduction to Agricultural Drones", "type" => "text", "description" => "What are agricultural drones? Types: multirotor (spraying, imaging), fixed-wing (large area surveying). Applications in Malawi: crop health assessment, pest scouting, fertilizer spraying, irrigation mapping. Benefits: save time, reduce costs, precision agriculture. Legal framework: CAAW (Civil Aviation Authority of Malawi) regulations.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Drone Hardware & Specifications", "type" => "video", "description" => "Popular models for agriculture: DJI Agras T30 (spraying), DJI Phantom 4 RTK (mapping), senseFly eBee (surveying). Key specs: flight time (20-45 min), payload (5-30kg), camera resolution, GPS accuracy. Choosing the right drone for your needs. Maintenance: propellers, batteries, calibration. Cost range: MWK 500,000-15,000,000.", "duration_minutes" => 35, "is_free_preview" => true],
                ["title" => "Drone Flight Planning & Controls", "type" => "video", "description" => "Pre-flight checklist: battery charge, propellers, GPS signal, weather conditions. Flight modes: manual, waypoint (GPS-guided), orbit. Planning a survey mission: set altitude (30-120m), overlap (70-80%), ground speed. Using flight planning apps: DJI GS Pro, Pix4Dcapture. Safety: no-fly zones, line of sight, maximum altitude.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Aerial Imaging & NDVI Analysis", "type" => "video", "description" => "Multispectral imaging: capturing red, green, blue, and near-infrared (NIR) light. NDVI (Normalized Difference Vegetation Index): measures plant health. NDVI values: 0.1-0.3 (bare soil/sparse), 0.4-0.6 (moderate), 0.7-1.0 (dense, healthy). Software: Pix4Dmapper, DroneDeploy, QGIS (free). Creating NDVI maps of your fields.", "duration_minutes" => 40, "is_free_preview" => false],
                ["title" => "Crop Spraying with Drones", "type" => "video", "description" => "Drone spraying benefits: 40-60% less pesticide used, even coverage, no soil compaction, operator safety. Setup: mix chemicals in drone tank, set spray rate (1-5 L/ha), flight speed (3-7 m/s). Calibration: spray nozzle, flow rate. Calibration flights: swath width, droplet size. Example: spraying 1 hectare in 10-15 minutes.", "duration_minutes" => 40, "is_free_preview" => false],
                ["title" => "Irrigation & Drainage Mapping", "type" => "text", "description" => "Using drones to map water flow: thermal cameras for moisture detection, elevation mapping for drainage planning. Applications in Malawi: designing irrigation schemes along Shire River, identifying waterlogged areas, planning terraces. Creating digital elevation models (DEM) from drone surveys.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Livestock Monitoring with Drones", "type" => "text", "description" => "Drones for livestock: counting herds in large paddocks, locating lost animals, monitoring grazing patterns. Thermal cameras: detect sick animals (fever). Applications in Malawi: game reserves, large ranches (Ethanol Company estates), dairy farms. Limitations: battery life, noise disturbing animals, regulations.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Data Processing & Analysis", "type" => "video", "description" => "Post-flight workflow: download images, process into orthomosaics, generate NDVI maps, analyze with GIS software. Cloud processing: DroneDeploy, Pix4D Cloud. Local processing: QGIS + OpenDroneMap (free). Creating actionable insights: zone maps for variable rate application, yield prediction maps.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Setting Up a Drone Service Business", "type" => "text", "description" => "Drone service opportunity in Malawi: growing demand from commercial farms, estates, government projects. Business model: per-hectare pricing (MWK 5,000-15,000/ha for spraying, MWK 3,000-8,000/ha for mapping). Getting clients: farmer cooperatives, estates, NGOs. CAAW certification requirements. Marketing through AgriTech Pro.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Regulatory Compliance & Safety", "type" => "text", "description" => "CAAW regulations: pilot licensing, drone registration, insurance requirements. Operational limits: visual line of sight (VLOS), maximum altitude 120m, no flying over people. Spraying regulations: approved chemicals, buffer zones, notification to neighbors. Insurance: third-party liability. Record keeping requirements.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Future of Drones in Malawian Agriculture", "type" => "text", "description" => "Emerging technologies: AI-powered crop analysis, autonomous swarming drones, drone-seeding. Government initiatives: drone corridor (Lilongwe-Dowa), partnership with University of Malawi. Integration with IoT sensors, satellite data, weather stations. Building local capacity: training programs, youth employment opportunities.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Getting Started", "type" => "text", "description" => "Key concepts review. Getting started: affordable options (rent vs. buy), training resources, joining drone operator networks in Malawi. Practice areas: open fields away from airports. Continuing education: online courses, manufacturer training. Building your portfolio: document your drone work for potential clients.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Soybeans Production Guide" => [
                ["title" => "Why Grow Soybeans in Malawi?", "type" => "text", "description" => "Soybeans: high-value legume crop, fixes nitrogen (reduces fertilizer costs by 30-40%). Market demand: pressed for oil, cake for animal feed (poultry, dairy). Major buyers: Phalombe Cotton Company, TransGlobe, small-scale processors. Price: MWK 500-800/kg. Growing regions: Balaka, Machinga, Mangochi, Kasungu.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Variety Selection & Seed Sourcing", "type" => "video", "description" => "Recommended varieties: NASPOT 6, NASPOT 7, NAMBESE 1 (early maturing 90 days), Makwacha (drought-tolerant). Seed sources: Seed Unit (MoA), Kagera Seed, private agro-dealers. Seed rate: 60-80 kg/ha. Inoculation: Rhizobium bacteria for first-time soybean fields. Germination test: wet towel method, aim for >80% germination.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Land Preparation & Planting", "type" => "video", "description" => "Soil requirements: well-drained, pH 6.0-7.0, low nitrogen soil suits soybeans. Land preparation: plough and harrow to fine tilth. Planting time: November-December with onset of rains. Spacing: 50cm between rows, 5-10cm between plants (or 1 seed per station). Depth: 3-5cm. Inoculate seed before planting.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Nutrient Management", "type" => "video", "description" => "Soybeans fix atmospheric nitrogen through root nodules. Still need: phosphorus (TSP/DAP at 100-150 kg/ha), potassium (MOP if deficient), sulfur. Micronutrients: molybdenum (critical for N-fixation), zinc. Do NOT apply high nitrogen fertilizer (reduces nodulation). Inter-cropping with maize: 2 rows maize : 1 row soybean.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Pest & Disease Management", "type" => "video", "description" => "Major pests: bean fly (early season, use seed treatment), aphids (spray neem or pirimicarb), pod borers (spray at flowering). Diseases: bacterial blight (avoid overhead watering, use resistant varieties), rust (spray fungicide), charcoal rot (crop rotation, no-till). Integrated approach: resistant varieties + cultural practices + targeted spraying.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Weed Management", "type" => "text", "description" => "Critical period: first 6 weeks. Pre-emergence herbicide: Dual Gold (metolachlor) or Dual Magnum. Post-emergence: Basagran (bentazon) at 3-4 weeks. Manual weeding: 2 passes minimum. Intercropping: reduces weed pressure. Soybeans are poor competitors with weeds due to slow early growth.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Harvesting & Post-Harvest", "type" => "video", "description" => "Harvest timing: when 80-90% of pods turn brown/yellow. Methods: manual pulling or cutting at base. Drying: spread on tarpaulin in sun for 3-5 days. Threshing: beat pods on tarpaulin or use mechanical thresher. Moisture content for storage: 12% or less. Yield: 1-2.5 tonnes/ha depending on management.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Marketing & Value Addition", "type" => "text", "description" => "Marketing channels: ADMARC, private traders, direct to processors. Contract farming: TransGlobe, Bwanje Cotton. Value addition: soybean oil extraction (small-scale presses MWK 150,000-500,000), soy milk, tofu, roasted soybeans. Soya chunks (mealie-soya blend): high demand. Quality standards: moisture <13%, damage <5%.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Soybeans in Rotation Systems", "type" => "text", "description" => "Benefits: breaks pest cycles, fixes nitrogen for next crop, improves soil structure. Recommended rotation: Year 1 Maize, Year 2 Soybeans, Year 3 Maize. 3-year rotation can increase maize yields by 20-30%. Intercropping options: soybean + groundnut, soybean + sorghum.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Market Links", "type" => "text", "description" => "Key takeaways: variety selection, proper inoculation, nutrient management, timely harvest. Market connections through AgriTech Pro. Next steps: soil testing, join soybean grower groups, contact extension officer for local variety recommendations.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Poultry Farming for Profit" => [
                ["title" => "Poultry Farming Opportunities in Malawi", "type" => "text", "description" => "Poultry demand in Malawi: growing urban population (Lilongwe 1.2M, Blantyre 1M). Chicken meat and eggs in high demand year-round. Two systems: extensive (free-range) and intensive (deep litter). Starting capital: MWK 100,000-500,000 for 50-200 birds. Potential income: MWK 2,000-5,000/bird cycle (8-12 weeks broilers).", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Housing & Equipment", "type" => "video", "description" => "Poultry house design: deep litter system (1m²/8-10 broilers, 1m²/4-5 layers). Materials: brick walls, iron sheet roof, chicken wire mesh. Ventilation: open-sided with curtains for wind protection. Equipment: feeders, drinkers (nipple or bell), lighting (warm white bulbs). Heating: brooder with charcoal or electric lamp for chicks.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Day-Old Chick Management", "type" => "video", "description" => "Sources of day-old chicks in Malawi: INDEFEED, Chikho Poultry, Iris. Brooding period (0-4 weeks): temperature 33-35°C first week, reduce 2°C weekly. Feed: starter mash/crumbles (21-23% protein). Water: clean, warm first 3 days, add electrolytes/vitamins. Vaccination schedule: Newcastle day 1, Gumboro weeks 2-3.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Feed Management & Nutrition", "type" => "video", "description" => "Feed types: starter (0-4 weeks), grower (5-8 weeks), finisher (9-12 weeks) for broilers. Layer feed: layers mash (16-18% protein). Feed ingredients available in Malawi: maize (energy), soybean meal (protein), fish meal, oyster shell (calcium), premix. Feed conversion ratio: 1.8-2.0 kg feed/kg gain (broilers). Feed cost: 60-70% of total production cost.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Disease Prevention & Biosecurity", "type" => "video", "description" => "Critical diseases: Newcastle (vaccinate!), Infectious Bursal Disease (Gumboro), Coccidiosis (amprolium in water), Marek's Disease. Biosecurity: restrict visitors, disinfect footwear, separate flocks. Signs of illness: drooping, loss of appetite, diarrhea, respiratory distress. Quarantine sick birds. Vaccination schedule chart.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Broiler Production (8-12 Weeks)", "type" => "video", "description" => "Target weight: 1.8-2.5 kg at 8-12 weeks. Growth milestones: 200g at 2 weeks, 500g at 4 weeks, 1.2kg at 8 weeks. Marketing: sell to restaurants, butcheries, direct to consumers. Pricing: MWK 3,000-5,000 per dressed bird. Multiple batches per year: 4-5 cycles. Restocking: clean house, new litter between batches.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Layer Production & Egg Management", "type" => "video", "description" => "Point of lay: 18-20 weeks. Housing: deep litter or battery cages. Peak production: 80-90% at 25-35 weeks. Daily management: collect eggs 2-3 times daily, clean and grade, store in cool room. Egg grades: A (60g+), B (55-60g), C (<55g). Marketing: direct to consumers MWK 150-250/egg, wholesale MWK 100-150/egg. Production life: 72-80 weeks.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Record Keeping & Financial Management", "type" => "text", "description" => "Records to keep: mortality rates, feed consumption, egg production, expenses, income. Key metrics: feed conversion ratio, cost of production per kg, mortality rate (<5% target), egg production rate. Break-even analysis: how many eggs/birds needed to cover costs. Using simple record sheets or AgriTech Pro app.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Marketing & Business Growth", "type" => "text", "description" => "Marketing strategies: direct to consumers (door-to-door, market), restaurants and hotels, butcheries, supermarkets. Building a customer base: consistent supply, quality, reliability. Growing: start with 50-100 birds, scale to 500-1000. Cooperative formation: bulk buying, shared transport. Government support: Small and Medium Enterprise Development Fund.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Action Plan", "type" => "text", "description" => "Review: housing, feeding, health, marketing. Action plan: write your poultry business plan, source day-old chicks, set up housing. Resources: INDEFEED for chicks, MoA extension officers, AgriTech Pro poultry community.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Post-Harvest Handling & Storage" => [
                ["title" => "Why Post-Harvest Losses Matter", "type" => "text", "description" => "Post-harvest losses in Malawi: 30-40% for cereals, 40-50% for fruits and vegetables. Causes: improper drying, poor storage, pests, moisture. Economic impact: farmers lose MWK 50,000-200,000/season. Food security: losses reduce available food for families. Aflatoxin contamination: health risk, market rejection.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Harvesting Best Practices", "type" => "video", "description" => "Harvesting at the right time: maturity indicators for maize (husks brown), beans (pods dry), groundnuts (leaves yellow). Harvesting methods: manual (sickle, hand picking), mechanical (combines). Minimizing field losses: careful handling, clean containers, shade drying. Timing: early morning or late afternoon to reduce shattering.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Drying Methods & Moisture Management", "type" => "video", "description" => "Target moisture: maize 12-13%, beans 12%, groundnuts 8-9%. Drying methods: sun drying on tarpaulins (free, 3-5 days), mechanical dryers (fast but expensive). Moisture meter testing: affordable digital meters available (MWK 15,000-50,000). dangers of under-drying: mold, aflatoxin. Over-drying: cracking, quality loss.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Threshing & Cleaning", "type" => "video", "description" => "Threshing methods: manual (beating with sticks), mechanical threshers (available at ADMARC). Cleaning: winnowing (using wind or fans), sieving to remove stones and debris. Grain quality grading: remove broken grains, discolored seeds, foreign matter. Cleaning increases market value by 10-20%.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Storage Technologies", "type" => "video", "description" => "Improved storage options: hermetic bags (PICS bags MWK 500-1,000), metal silos (MWK 30,000-80,000), plastic silos (MWK 20,000-50,000), community warehouses. Hermetic storage: creates low-oxygen environment, kills insects without chemicals. Traditional methods: ash mixing (for beans), smoking (for fish). Storage conditions: dry, cool, well-ventilated.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Pest Management in Storage", "type" => "video", "description" => "Common storage pests: weevils (maize, beans), grain borers, rodents, birds. Prevention: proper drying, clean storage containers, hygiene. Control: neem leaf powder (natural insecticide), Diatomaceous earth, hermetic storage. Chemical control: Actellic dust (use sparingly, food safety concerns). Rodent control: traps, metal silos, clean storage area.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Aflatoxin Prevention & Testing", "type" => "video", "description" => "Aflatoxin: toxic mold metabolite, causes liver cancer, stunts child growth. Risk factors: drought-stressed crops, improper drying, damaged grains, warm storage. Prevention: harvest at maturity, dry quickly, sort/remove damaged grains, hermetic storage. Testing: AflaCheck rapid test kits (MWK 5,000-10,000), laboratory testing at Bvumbwe Research Station.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Value Addition Through Processing", "type" => "text", "description" => "Processing options: milling (maize to flour), oil pressing (soybean, groundnut), drying (fruits, vegetables, fish). Equipment: hammer mills (MWK 200,000-1,000,000), oil presses, solar dryers. Licensing: MBFCA (Malawi Bureau of Standards). Quality standards: must meet MBS standards for marketed products.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Resources", "type" => "text", "description" => "Key takeaways: harvest at right time, dry properly, store in hermetic containers, test for aflatoxin. Resources: ADMARC training, MoA extension, CIMMYT post-harvest guidelines, AgriTech Pro community. Start small: invest in PICS bags this season.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Climate-Smart Agriculture" => [
                ["title" => "Climate Change & Malawian Agriculture", "type" => "text", "description" => "Climate change impacts on Malawi: rising temperatures (+1°C since 1980), erratic rainfall, increased droughts and floods. Agriculture is 30% of GDP, 80% of employment. Vulnerable crops: maize (most affected), tobacco, tea. Climate-smart agriculture (CSA): sustainably increase productivity, build resilience, reduce emissions.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Conservation Agriculture", "type" => "video", "description" => "Three principles: minimum tillage (no ploughing, use hoe to make planting basins), permanent soil cover (mulch with crop residues), crop rotation (alternate cereals with legumes). Benefits: improved soil structure, moisture retention, reduced erosion. Adoption in Malawi: promoted by FAO, CIMMYT, Total LandCare. Starting: one field first, observe results.", "duration_minutes" => 35, "is_free_preview" => true],
                ["title" => "Drought-Tolerant Crop Varieties", "type" => "video", "description" => "Drought-tolerant varieties available in Malawi: maize (MH18, ZS242, DK8031), sorghum (IKMP-5, Gadam), millet (Kaphalire), cassava (Magolela, Chamba). Why diversify from maize: sorghum/millet tolerate 300-500mm rainfall, maize needs 500-800mm. Seed access: ADMARC, Seed Unit, agro-dealers. Introducing orphan crops for food security.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Water Harvesting & Management", "type" => "video", "description" => "Techniques: contour bunds, grass strips (vetiver), half-moon water harvesting, zaï pits (planting holes), mulching. Farm ponds: excavate 10m x 10m x 2m, line with clay or plastic. Roof rainwater harvesting: 1mm rain = 1L/m². Community dams and weirs. Government programs: National Water Resources Master Plan.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Agroforestry & Carbon Farming", "type" => "video", "description" => "Agroforestry: integrating trees with crops and/or livestock. Species for Malawi: Faidherbia albida (fixes nitrogen, drops leaves in growing season), Gliricidia sepium (green manure, live fence), Moringa oleifera (nutritious leaves). Benefits: shade, windbreaks, firewood, fodder, soil improvement. Carbon farming: payments for sequestering carbon.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Integrated Soil Fertility Management", "type" => "text", "description" => "Combining organic and mineral nutrient sources: compost + fertilizer. Micro-dosing: applying small amounts of fertilizer (1-2g per planting station). Green manures: Tephrosia, Mucuna, Crotalaria. Legume inter-cropping: fixes nitrogen. Soil testing: use kits from ADD offices. Rebuilding degraded soils common in Southern Malawi.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Livestock & Climate Adaptation", "type" => "text", "description" => "Climate impacts on livestock: heat stress, reduced forage quality, water scarcity. Adaptations: improved breeds (tolerant to local conditions), better feeding (Napier grass, crop residues), water harvesting for livestock. Silvopastoral systems: combining trees, forage, and livestock. Disease management: changing disease patterns with climate.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Crop Insurance & Risk Transfer", "type" => "text", "description" => "Index-based weather insurance: pays out when rainfall falls below threshold. Pilot programs in Malawi: ACRE Africa, Pula Advisory. Government crop insurance discussions. Savings as self-insurance: VSLA approach. Diversification: multiple crops, livestock, off-farm income. Early warning systems: Malawi Department of Climate Change and Meteorological Services.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Market Access for Climate-Smart Products", "type" => "text", "description" => "Premium markets: organic certification, Fair Trade, carbon credits. Carbon farming: voluntary carbon markets (Verra, Gold Standard). Payments: USD 10-30 per tonne CO2 sequestered. Aggregation through cooperatives. Using AgriTech Pro to connect with climate-conscious buyers.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Climate Action Plan", "type" => "text", "description" => "Key CSA practices: conservation agriculture, drought-tolerant varieties, water harvesting, agroforestry. Create your climate action plan: assess your climate risks, select 2-3 adaptation strategies, implement this season. Resources: MoA, CIMMYT, FAO, UNDP Climate Change Programme Malawi.", "duration_minutes" => 20, "is_free_preview" => false],
            ],
        ];

        foreach ($lessonData as $courseTitle => $lessons) {
            $course = Course::where('title', $courseTitle)->first();
            if (!$course) continue;

            foreach ($lessons as $index => $lesson) {
                Lesson::create([
                    'course_id' => $course->id,
                    'title' => $lesson['title'],
                    'description' => $lesson['description'],
                    'sort_order' => $index + 1,
                    'type' => $lesson['type'],
                    'duration_minutes' => $lesson['duration_minutes'],
                    'is_free_preview' => $lesson['is_free_preview'],
                    'is_published' => true,
                ]);
            }
        }

        $this->command->info("    ✓ " . Lesson::count() . " lessons seeded");
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
                "thumbnail" => "agri-products/maize-seed.jpg",
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
                "thumbnail" => "agri-products/fertilizer.jpg",
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
                "thumbnail" => "agri-products/tomato.jpg",
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
                "thumbnail" => "agri-products/goats.jpg",
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
                "thumbnail" => "agri-products/fertilizer.jpg",
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
                "thumbnail" => "agri-products/water-pump.jpg",
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
                "thumbnail" => "agri-products/cabbage.jpg",
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
                "thumbnail" => "agri-products/insecticide.jpg",
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
                "thumbnail" => "agri-products/soybean.jpg",
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
                "thumbnail" => "agri-products/water-pump.jpg",
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
                "thumbnail" => "agri-products/groundnut.jpg",
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
                "thumbnail" => "agri-products/dairy-cow.jpg",
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
                    "Submit your best farming innovation for a chance to win K 50,000 in prizes! We are looking for practical, affordable solutions that smallholder farmers in Malawi can adopt immediately.",
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

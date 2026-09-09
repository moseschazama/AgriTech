<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        \App\Models\LessonProgress::query()->delete();
        Lesson::query()->delete();

        $lessonData = [
            "Modern Maize Production in Malawi" => [
                ["title" => "Introduction to Maize Farming in Malawi", "type" => "text", "description" => "Overview of maize as Malawi's staple crop. Maize accounts for over 60% of caloric intake. Learn about the major growing regions: Central (Lilongwe, Kasungu, Ntchisi), Southern (Thyolo, Mulanje, Chiradzulu), and Northern (Mzimba, Rumphi). Understanding the maize calendar: planting in November-December, harvesting in April-May.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Land Preparation & Soil Testing", "type" => "video", "description" => "How to prepare land for maize in Malawi using both manual (hoe) and ox-plough methods. Soil testing kits available from Agricultural Development Division (ADD) offices. Understanding soil pH requirements (5.5-7.0 for maize). Timing of land preparation: early ploughing after first rains in October-November.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Seed Selection & Planting", "type" => "video", "description" => "Choosing the right maize varieties for Malawi: DK8031, MH18, ZS242, and OPV varieties like Kalima. Seed rates: 20-25 kg/ha for hybrids, 10-12 kg/ha for OPVs. Planting depth: 3-5cm. Spacing: 75cm between rows, 25cm between plants.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Fertilizer Application (Basal & Top Dressing)", "type" => "video", "description" => "Understanding fertilizer types available in Malawi: NPK 23:21:0+4S (basal), CAN 27% N (top dressing), Urea 46% N. Application rates: 1 bag NPK per acre at planting, 1 bag CAN per acre at 4-6 weeks after emergence.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Pest Management: Fall Armyworm Control", "type" => "video", "description" => "Identifying Fall Armyworm damage: holes in leaves, frass in leaf whorl. Scouting methods. Chemical controls: Coragen, Ampligo. Biological controls. IPM approach recommended by Malawi MoA.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Disease Management: Maize Lethal Necrosis & Streak Virus", "type" => "video", "description" => "Identifying Maize Lethal Necrosis and Maize Streak Virus. Prevention: certified seed, crop rotation, resistant varieties from CIMMYT.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Weed Management Strategies", "type" => "text", "description" => "Critical weeding periods: first at 3-4 weeks, second at 6-8 weeks. Manual vs pre-emergence herbicides. Conservation agriculture: mulching, minimum tillage.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Water Management & Rainfed Farming", "type" => "text", "description" => "Most maize in Malawi is rainfed. Rainfall patterns by region. Conservation agriculture for moisture retention. Supplemental irrigation options.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Harvesting & Post-Harvest Handling", "type" => "video", "description" => "Knowing when to harvest. Harvesting methods: manual vs mechanical shellers. Moisture management and drying.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Storage & Aflatoxin Prevention", "type" => "video", "description" => "Storage options: hermetic bags (PICS), metal silos, community warehouses. Aflatoxin prevention and testing with AflaCheck kits.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Marketing Your Maize: ADMARC & Private Buyers", "type" => "text", "description" => "Selling options: ADMARC guaranteed price, private traders, local markets. Contract farming. Using AgriTech Pro marketplace.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Next Steps", "type" => "text", "description" => "Review of key concepts. Next steps: join farmer groups, access AIP, apply for ADMARC loans. Recommended resources.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Profitable Dairy Farming" => [
                ["title" => "Dairy Farming Overview in Malawi", "type" => "text", "description" => "The dairy industry in Malawi: major players include Kal Dairy, Suncrest Creameries, and smallholder cooperatives. Milk demand exceeds supply. Key breeds: Holstein-Friesian, Jersey, Crosses.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Breed Selection & Housing", "type" => "video", "description" => "Choosing breeds for Malawi conditions. Housing requirements: ventilated, raised floor, drainage. Construction using local materials.", "duration_minutes" => 35, "is_free_preview" => true],
                ["title" => "Feed & Nutrition Management", "type" => "video", "description" => "Feed requirements: Napier grass, maize bran, soybean meal. Daily ration: 15-20kg Napier grass + 3-5kg concentrates per cow.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Milk Production & Hygiene", "type" => "video", "description" => "Milking procedures, hygiene critical, milk handling and cooling. Testing: lactometer, alcohol test.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Reproduction & Breeding", "type" => "video", "description" => "Heat detection, Artificial Insemination services from LITC. Gestation: 283 days. Calving management.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Disease Prevention & Treatment", "type" => "video", "description" => "Common diseases: Mastitis, Brucellosis, FMD, East Coast Fever. Vaccination schedule.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Dairy Economics & Record Keeping", "type" => "text", "description" => "Cost-benefit analysis, monthly costs, revenue projections, break-even analysis, record keeping methods.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Dairy Value Chains in Malawi", "type" => "text", "description" => "Marketing channels, value addition (ghee, yoghurt, cheese), mini processing units, cooperative formation.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Waste Management & Biogas", "type" => "text", "description" => "Cow dung utilization: biogas production, composting, fuel briquettes. Malawi Biogas Programme.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Accessing Finance for Dairy Expansion", "type" => "text", "description" => "Financial options: NBS Bank, FDH Bank, Microfinance institutions. Government programs and business plan templates.", "duration_minutes" => 25, "is_free_preview" => false],
            ],

            "Drip Irrigation Setup & Management" => [
                ["title" => "Why Irrigation Matters in Malawi", "type" => "text", "description" => "Malawi receives erratic rainfall. Only 3% of irrigable land is irrigated. Irrigation can increase yields 2-4x.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Types of Irrigation Systems", "type" => "video", "description" => "Overview: flood, sprinkler, drip. Drip irrigation best for smallholders: saves 40-60% water. Components.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "System Design & Layout", "type" => "video", "description" => "Designing for a 50m x 30m plot. Materials needed, cost estimate MWK 35,000-60,000.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Installation Step-by-Step", "type" => "video", "description" => "Header tank, mainline, sub-mains, laterals, drippers installation. Flushing and leak checking.", "duration_minutes" => 40, "is_free_preview" => false],
                ["title" => "Water Source Options", "type" => "text", "description" => "Rainwater harvesting, borehole, river pumping. Solar pump systems. Rain tank options.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Crop Scheduling & Water Management", "type" => "video", "description" => "Water requirements by crop. Scheduling system runs. Monitoring soil moisture.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "System Maintenance & Repair", "type" => "video", "description" => "Daily, weekly, monthly, seasonal maintenance. Common problems and troubleshooting. Spare parts.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Fertigation: Feeding Through Drip Lines", "type" => "text", "description" => "Applying soluble fertilizer through drip. Compatible fertilizers, mixing, Venturi injector. 30% less fertilizer needed.", "duration_minutes" => 25, "is_free_preview" => false],
            ],

            "Organic Vegetable Production" => [
                ["title" => "Introduction to Organic Farming in Malawi", "type" => "text", "description" => "Organic farming principles. Benefits: lower input costs, premium prices. Certification: MOAM, GlobalG.A.P.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Soil Building & Composting", "type" => "video", "description" => "Composting methods: heap, pit, vermicomposting. Ingredients, C:N ratio, layering technique.", "duration_minutes" => 35, "is_free_preview" => true],
                ["title" => "Organic Pest & Disease Control", "type" => "video", "description" => "Neem spray, chili-garlic spray, ash dusting. Companion planting and crop rotation.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Growing Tomatoes Organically", "type" => "video", "description" => "Variety selection, seedlings, transplanting, staking, organic fertilization, harvesting.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Growing Indigenous Vegetables (Masamba)", "type" => "video", "description" => "Chambu, khutsiya, chimphamba, nyemba. High in vitamins. Growing conditions and harvesting.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Cabbage & Leafy Greens Production", "type" => "video", "description" => "Cabbage varieties, transplanting, organic pest control. Growing sukuma wiki.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Onion & Garlic Production", "type" => "text", "description" => "Onion varieties, seedbed, transplanting, bulbing, harvesting, curing. Market prices.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Organic Certification & Market Access", "type" => "text", "description" => "MOAM inspection, costs, markets (Shoprite, export). Premium prices 20-50% above conventional.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Packaging & Post-Harvest for Organic Produce", "type" => "text", "description" => "Packaging standards, cooling methods, transport, shelf life extension.", "duration_minutes" => 20, "is_free_preview" => false],
                ["title" => "Course Summary & Organic Farm Business Plan", "type" => "text", "description" => "Key concepts review, creating a business plan, record keeping, building a farming brand.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Agribusiness Finance for Farmers" => [
                ["title" => "Financial Literacy Basics", "type" => "text", "description" => "Money management for farming. Income, expenses, profit, cash flow. Setting financial goals. Budgeting.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Farm Record Keeping", "type" => "video", "description" => "Essential records: field, financial, livestock. Simple bookkeeping. Digital tools.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Farm Budgeting & Planning", "type" => "video", "description" => "Seasonal budget creation, income estimation, cash flow planning, contingency.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Understanding Credit & Loans", "type" => "text", "description" => "Types of credit: formal, semi-formal, informal. Interest rates. Understanding APR and loan terms.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Accessing Agricultural Credit", "type" => "text", "description" => "Financial institutions, microfinance, government programs. Requirements and application tips.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Savings Groups & VSLAs", "type" => "video", "description" => "Village Savings and Loan Associations: formation, operations, benefits. Step-by-step guide.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Market Analysis & Pricing Strategies", "type" => "text", "description" => "Seasonal price fluctuations, price data sources, pricing strategies, finding markets.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Value Addition & Processing", "type" => "text", "description" => "Adding value: drying, milling, pressing, packaging. Equipment costs, licensing.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Insurance & Risk Management", "type" => "text", "description" => "Farm risk management, diversification, insurance options, contract farming, government relief.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Tax Obligations for Farmers", "type" => "text", "description" => "Turnover Tax, Income Tax, TPIN registration with MRA. Filing returns. Exemptions.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Business Registration & Formalization", "type" => "text", "description" => "Registration options: sole proprietorship, cooperative, limited company. Process and costs.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Writing a Business Plan", "type" => "video", "description" => "Business plan structure, templates, financial projections. Tips for loan applications.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Success Stories: Malawian Farmer Entrepreneurs", "type" => "text", "description" => "Case studies from Kasungu, Mangochi, Lilongwe. Lessons learned.", "duration_minutes" => 20, "is_free_preview" => false],
                ["title" => "Course Summary & Action Plan", "type" => "text", "description" => "Key takeaways, personal action plan, resources.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Drone Technology in Modern Farming" => [
                ["title" => "Introduction to Agricultural Drones", "type" => "text", "description" => "What are agricultural drones? Types, applications in Malawi. Legal framework: CAAW regulations.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Drone Hardware & Specifications", "type" => "video", "description" => "Popular models: DJI Agras T30, Phantom 4 RTK, senseFly eBee. Key specs and cost range.", "duration_minutes" => 35, "is_free_preview" => true],
                ["title" => "Drone Flight Planning & Controls", "type" => "video", "description" => "Pre-flight checklist, flight modes, planning apps, safety regulations.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Aerial Imaging & NDVI Analysis", "type" => "video", "description" => "Multispectral imaging, NDVI values, software (Pix4D, DroneDeploy, QGIS).", "duration_minutes" => 40, "is_free_preview" => false],
                ["title" => "Crop Spraying with Drones", "type" => "video", "description" => "Benefits, setup, calibration, spray rates. 1 hectare in 10-15 minutes.", "duration_minutes" => 40, "is_free_preview" => false],
                ["title" => "Irrigation & Drainage Mapping", "type" => "text", "description" => "Thermal cameras for moisture detection, elevation mapping, DEM creation.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Livestock Monitoring with Drones", "type" => "text", "description" => "Counting herds, locating animals, thermal cameras for health monitoring.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Data Processing & Analysis", "type" => "video", "description" => "Post-flight workflow, orthomosaics, NDVI maps, cloud and local processing.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Setting Up a Drone Service Business", "type" => "text", "description" => "Business model, per-hectare pricing, getting clients, CAAW certification.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Regulatory Compliance & Safety", "type" => "text", "description" => "CAAW regulations, pilot licensing, insurance, operational limits.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Future of Drones in Malawian Agriculture", "type" => "text", "description" => "AI-powered analysis, autonomous swarming, drone corridor, youth employment.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Getting Started", "type" => "text", "description" => "Key concepts review, getting started, practice areas, continuing education.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Soybeans Production Guide" => [
                ["title" => "Why Grow Soybeans in Malawi?", "type" => "text", "description" => "Soybeans: high-value legume, nitrogen fixing. Market demand and prices. Growing regions.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Variety Selection & Seed Sourcing", "type" => "video", "description" => "Recommended varieties: NASPOT 6, NASPOT 7, NAMBESE 1, Makwacha. Seed sources and rates.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Land Preparation & Planting", "type" => "video", "description" => "Soil requirements, land preparation, planting time, spacing, depth, inoculation.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Nutrient Management", "type" => "video", "description" => "Phosphorus, potassium, sulfur needs. Micronutrients. Intercropping with maize.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Pest & Disease Management", "type" => "video", "description" => "Bean fly, aphids, pod borers. Bacterial blight, rust, charcoal rot. IPM approach.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Weed Management", "type" => "text", "description" => "Pre-emergence and post-emergence herbicides. Manual weeding. Intercropping benefits.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Harvesting & Post-Harvest", "type" => "video", "description" => "Harvest timing, methods, drying, threshing, moisture content for storage.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Marketing & Value Addition", "type" => "text", "description" => "Marketing channels, contract farming, oil extraction, soy milk, quality standards.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Soybeans in Rotation Systems", "type" => "text", "description" => "Benefits of rotation, recommended sequences, intercropping options.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Market Links", "type" => "text", "description" => "Key takeaways, market connections through AgriTech Pro, next steps.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Poultry Farming for Profit" => [
                ["title" => "Poultry Farming Opportunities in Malawi", "type" => "text", "description" => "Growing demand in urban areas. Two systems: extensive and intensive. Starting capital and potential income.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Housing & Equipment", "type" => "video", "description" => "Deep litter system design, materials, ventilation, equipment requirements.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Day-Old Chick Management", "type" => "video", "description" => "Sources in Malawi, brooding period, temperature management, feed, vaccination schedule.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Feed Management & Nutrition", "type" => "video", "description" => "Feed types, ingredients available in Malawi, feed conversion ratio, cost breakdown.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Disease Prevention & Biosecurity", "type" => "video", "description" => "Critical diseases, biosecurity measures, signs of illness, vaccination schedule.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Broiler Production (8-12 Weeks)", "type" => "video", "description" => "Growth milestones, marketing, pricing, multiple batches per year.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Layer Production & Egg Management", "type" => "video", "description" => "Point of lay, housing, production rates, egg grading, marketing.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Record Keeping & Financial Management", "type" => "text", "description" => "Essential records, key metrics, break-even analysis.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Marketing & Business Growth", "type" => "text", "description" => "Marketing strategies, customer base, scaling, cooperative formation.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Action Plan", "type" => "text", "description" => "Review, action plan, resources for next steps.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Post-Harvest Handling & Storage" => [
                ["title" => "Why Post-Harvest Losses Matter", "type" => "text", "description" => "Post-harvest losses in Malawi: 30-40% for cereals. Causes, economic impact, aflatoxin risk.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Harvesting Best Practices", "type" => "video", "description" => "Maturity indicators, harvesting methods, minimizing field losses, timing.", "duration_minutes" => 30, "is_free_preview" => true],
                ["title" => "Drying Methods & Moisture Management", "type" => "video", "description" => "Target moisture levels, sun drying, mechanical dryers, moisture meter testing.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Threshing & Cleaning", "type" => "video", "description" => "Manual and mechanical threshing, winnowing, sieving, quality grading.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Storage Technologies", "type" => "video", "description" => "Hermetic bags, metal silos, plastic silos, community warehouses. Hermetic storage principles.", "duration_minutes" => 35, "is_free_preview" => false],
                ["title" => "Pest Management in Storage", "type" => "video", "description" => "Weevils, grain borers, rodents. Prevention, neem, Diatomaceous earth, rodent control.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Aflatoxin Prevention & Testing", "type" => "video", "description" => "Risk factors, prevention methods, AflaCheck rapid test kits, laboratory testing.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Value Addition Through Processing", "type" => "text", "description" => "Milling, oil pressing, drying. Equipment costs, MBFCA licensing, quality standards.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Resources", "type" => "text", "description" => "Key takeaways, resources, getting started with PICS bags.", "duration_minutes" => 20, "is_free_preview" => false],
            ],

            "Climate-Smart Agriculture" => [
                ["title" => "Climate Change & Malawian Agriculture", "type" => "text", "description" => "Climate change impacts: rising temperatures, erratic rainfall, droughts, floods. CSA definition.", "duration_minutes" => 25, "is_free_preview" => true],
                ["title" => "Conservation Agriculture", "type" => "video", "description" => "Three principles: minimum tillage, permanent soil cover, crop rotation. Benefits and adoption.", "duration_minutes" => 35, "is_free_preview" => true],
                ["title" => "Drought-Tolerant Crop Varieties", "type" => "video", "description" => "Maize, sorghum, millet, cassava varieties. Why diversify from maize. Seed access.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Water Harvesting & Management", "type" => "video", "description" => "Contour bunds, grass strips, half-moon harvesting, zaï pits, farm ponds, roof harvesting.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Agroforestry & Carbon Farming", "type" => "video", "description" => "Integrating trees: Faidherbia albida, Gliricidia, Moringa. Benefits. Carbon farming payments.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Integrated Soil Fertility Management", "type" => "text", "description" => "Combining organic and mineral sources. Micro-dosing. Green manures. Soil testing.", "duration_minutes" => 30, "is_free_preview" => false],
                ["title" => "Livestock & Climate Adaptation", "type" => "text", "description" => "Heat stress, forage quality, water scarcity. Improved breeds, silvopastoral systems.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Crop Insurance & Risk Transfer", "type" => "text", "description" => "Index-based weather insurance, pilot programs, savings as self-insurance, diversification.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Market Access for Climate-Smart Products", "type" => "text", "description" => "Premium markets, organic certification, carbon credits, voluntary carbon markets.", "duration_minutes" => 25, "is_free_preview" => false],
                ["title" => "Course Summary & Climate Action Plan", "type" => "text", "description" => "Key practices, creating climate action plan, resources.", "duration_minutes" => 20, "is_free_preview" => false],
            ],
        ];

        $count = 0;
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
                $count++;
            }
        }

        $this->command->info("    ✓ {$count} lessons seeded across " . count($lessonData) . " courses");
    }
}

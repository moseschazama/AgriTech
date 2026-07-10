<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\District;
use App\Models\TradingCentre;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ══ NORTHERN REGION ══
            'Chitipa' => [
                'region' => 'Northern',
                'trading_centres' => ['Chitipa Boma','Kaning\'a','Mwenemwako','Nthalire','Chiwawa','Mafuta'],
            ],
            'Karonga' => [
                'region' => 'Northern',
                'trading_centres' => ['Karonga Boma','Chilumba','Uli','Livingstonia','Mwaya','Kafukule','Kyungu'],
            ],
            'Likoma' => [
                'region' => 'Northern',
                'trading_centres' => ['Likoma Boma','Chizumulu','Mzukula'],
            ],
            'Mzimba' => [
                'region' => 'Northern',
                'trading_centres' => ['Mzimba Boma','Mzuzu City','Enukweni','Embangweni','Ekwendeni','Khosolo','Mzambaya','Viphya'],
            ],
            'Nkhata Bay' => [
                'region' => 'Northern',
                'trading_centres' => ['Nkhata Bay Boma','Chinzali','Msuku','Nkhotakota Gate','Mpamba','Mzenga'],
            ],
            'Rumphi' => [
                'region' => 'Northern',
                'trading_centres' => ['Rumphi Boma','Phwezi','Chombe','Mlowe','Bolero','Chikulamayembe'],
            ],

            // ══ CENTRAL REGION ══
            'Dedza' => [
                'region' => 'Central',
                'trading_centres' => ['Dedza Boma','Mtakataka','Kaphirizombe','Linthipe','Nthulu','Kachere','Dedza Border'],
            ],
            'Dowa' => [
                'region' => 'Central',
                'trading_centres' => ['Dowa Boma','Mponela','Chakhaza','Nsadzu','Kasakula','Mtunthama','Kanthengwa'],
            ],
            'Kasungu' => [
                'region' => 'Central',
                'trading_centres' => ['Kasungu Boma','Lifupa','Chisepo','Songwe','Nkhamenya','Kasungu National Park','Ntchisi Gate'],
            ],
            'Lilongwe' => [
                'region' => 'Central',
                'trading_centres' => ['Lilongwe City','Area 1','Area 2','Area 3','Kanengo','Lumbadzi','Ntandile','Kalonga','Mchesi','Mkwinda','Lingadzi'],
            ],
            'Mchinji' => [
                'region' => 'Central',
                'trading_centres' => ['Mchinji Boma','Kamwendo','Msongwe','Kapiri','Khami','Mchinji Border','Gwirizani'],
            ],
            'Mangochi' => [
                'region' => 'Central',
                'trading_centres' => ['Mangochi Boma','Monkey Bay','Malombe','Cobbe Barracks','Makanjira','Limbwe','Nkungulu'],
            ],
            'Nkhotakota' => [
                'region' => 'Central',
                'trading_centres' => ['Nkhotakota Boma','Mbwana','Dwangwa','Ntchisi Gate','Bua','Maravi','Nkhotakota Wildlife Reserve'],
            ],
            'Ntchisi' => [
                'region' => 'Central',
                'trading_centres' => ['Ntchisi Boma','Chitsanzo','Ntchisi Forest','Kasakula','Mwadza','Thukuma'],
            ],
            'Salima' => [
                'region' => 'Central',
                'trading_centres' => ['Salima Boma','Sani','Chipoka','Malembo','Nkhotakota Gate','Salima Sugar','Liwonde Gate'],
            ],

            // ══ SOUTHERN REGION ══
            'Balaka' => [
                'region' => 'Southern',
                'trading_centres' => ['Balaka Boma','Liwonde','Namanowa','Kapiri','Mlambe','Utchi','Balaka Border'],
            ],
            'Blantyre' => [
                'region' => 'Southern',
                'trading_centres' => ['Blantyre City','Limbe','Machinjiri','Ndirande','Soche','Chileka','Chilobwe','Nancholi','Mandala','Sunny Side','Kabula'],
            ],
            'Chikwawa' => [
                'region' => 'Southern',
                'trading_centres' => ['Chikwawa Boma','Lengwe','Tsangano','Marka','Nsanje Gate','Makhwira','Kandeu'],
            ],
            'Chiradzulu' => [
                'region' => 'Southern',
                'trading_centres' => ['Chiradzulu Boma','Nsinde','Kadzakolo','Mbera','Ngombwa','Lipala'],
            ],
            'Machinga' => [
                'region' => 'Southern',
                'trading_centres' => ['Machinga Boma','Liwonde','Namanowa','Mikoko','Malingama','Nayuchi','Machinga Border'],
            ],
            'Mulanje' => [
                'region' => 'Southern',
                'trading_centres' => ['Mulanje Boma','Limbuli','Thuchila','Muloza','Mulanje Mission','Chambe','Bondwe'],
            ],
            'Mwanza' => [
                'region' => 'Southern',
                'trading_centres' => ['Mwanza Boma','Mwanza Border','Phalombe Gate','Neno Gate','Mwanza Hill','Thambani'],
            ],
            'Neno' => [
                'region' => 'Southern',
                'trading_centres' => ['Neno Boma','Lizulu','Mwami','Neno Parish','Luwani','Mphomwa'],
            ],
            'Ntcheu' => [
                'region' => 'Southern',
                'trading_centres' => ['Ntcheu Boma','Bilila','Kandeu','Mtakataka Gate','Ntcheu Parish','Goliati','Mchere'],
            ],
            'Phalombe' => [
                'region' => 'Southern',
                'trading_centres' => ['Phalombe Boma','Mikolongwe','Namiwawa','Chiringa','Nthalire','Dziwe','Phalombe Parish'],
            ],
            'Thyolo' => [
                'region' => 'Southern',
                'trading_centres' => ['Thyolo Boma','Bunga','Thekerani','Makwasa','Misuku','Thyolo Tea Estate','Thava'],
            ],
            'Zomba' => [
                'region' => 'Southern',
                'trading_centres' => ['Zomba City','Thondwe','Domasi','Mponda','Naisi','Ching\'ombe','Zomba Plateau'],
            ],
        ];

        foreach ($data as $districtName => $info) {
            $district = District::create([
                'name'   => $districtName,
                'region' => $info['region'],
            ]);

            foreach ($info['trading_centres'] as $centre) {
                TradingCentre::create([
                    'district_id' => $district->id,
                    'name'        => $centre,
                ]);
            }
        }
    }
}

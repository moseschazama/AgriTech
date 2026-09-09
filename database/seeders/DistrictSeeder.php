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
                'trading_centres' => [
                    'Chitipa Boma'     => [-10.1275, 33.4260],
                    'Kaning\'a'        => [-10.2800, 33.3100],
                    'Mwenemwako'       => [-9.9800, 33.5600],
                    'Nthalire'         => [-10.4200, 33.2400],
                    'Chiwawa'          => [-10.1900, 33.5000],
                    'Mafuta'           => [-10.0900, 33.3800],
                ],
            ],
            'Karonga' => [
                'region' => 'Northern',
                'trading_centres' => [
                    'Karonga Boma'     => [-9.9333, 33.9333],
                    'Chilumba'         => [-10.1167, 34.0667],
                    'Uli'              => [-9.8500, 33.8500],
                    'Livingstonia'     => [-10.6000, 34.1333],
                    'Mwaya'            => [-10.0500, 34.0500],
                    'Kafukule'         => [-9.7833, 33.9000],
                    'Kyungu'           => [-9.9667, 33.9667],
                ],
            ],
            'Likoma' => [
                'region' => 'Northern',
                'trading_centres' => [
                    'Likoma Boma'      => [-12.0500, 34.7333],
                    'Chizumulu'        => [-12.1167, 34.6167],
                    'Mzukula'          => [-12.0167, 34.7500],
                ],
            ],
            'Mzimba' => [
                'region' => 'Northern',
                'trading_centres' => [
                    'Mzimba Boma'      => [-11.7667, 33.6000],
                    'Mzuzu City'       => [-11.4500, 34.0167],
                    'Enukweni'         => [-11.8167, 33.5167],
                    'Embangweni'       => [-11.5667, 33.8333],
                    'Ekwendeni'        => [-11.3500, 33.9333],
                    'Khosolo'          => [-11.6167, 33.7500],
                    'Mzambaya'         => [-11.9000, 33.5000],
                    'Viphya'           => [-11.3333, 33.7833],
                ],
            ],
            'Nkhata Bay' => [
                'region' => 'Northern',
                'trading_centres' => [
                    'Nkhata Bay Boma'  => [-11.6000, 34.3000],
                    'Chinzali'         => [-11.4833, 34.2500],
                    'Msuku'            => [-11.5333, 34.1833],
                    'Nkhotakota Gate'  => [-12.3500, 34.3333],
                    'Mpamba'           => [-11.6833, 34.2167],
                    'Mzenga'           => [-11.5500, 34.3500],
                ],
            ],
            'Rumphi' => [
                'region' => 'Northern',
                'trading_centres' => [
                    'Rumphi Boma'      => [-11.0167, 33.8667],
                    'Phwezi'           => [-10.9167, 33.8000],
                    'Chombe'           => [-11.1167, 33.7833],
                    'Mlowe'            => [-11.2500, 33.9500],
                    'Bolero'           => [-10.8500, 33.7333],
                    'Chikulamayembe'   => [-10.9667, 33.8333],
                ],
            ],

            // ══ CENTRAL REGION ══
            'Dedza' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Dedza Boma'       => [-14.3333, 34.3333],
                    'Mtakataka'        => [-14.4167, 34.4667],
                    'Kaphirizombe'     => [-14.2833, 34.2833],
                    'Linthipe'         => [-14.2500, 34.4167],
                    'Nthulu'           => [-14.3833, 34.3667],
                    'Kachere'          => [-14.3000, 34.4000],
                    'Dedza Border'     => [-14.3667, 34.5000],
                ],
            ],
            'Dowa' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Dowa Boma'        => [-13.6500, 33.9333],
                    'Mponela'          => [-13.5333, 33.9167],
                    'Chakhaza'         => [-13.6167, 33.8833],
                    'Nsadzu'           => [-13.5667, 33.9500],
                    'Kasakula'         => [-13.6833, 33.9000],
                    'Mtunthama'        => [-13.5833, 34.0000],
                    'Kanthengwa'       => [-13.6333, 33.8667],
                ],
            ],
            'Kasungu' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Kasungu Boma'     => [-13.0333, 33.4833],
                    'Lifupa'           => [-13.0833, 33.5167],
                    'Chisepo'          => [-12.9500, 33.5500],
                    'Songwe'           => [-13.1333, 33.3833],
                    'Nkhamenya'        => [-12.9833, 33.6167],
                    'Kasungu National Park' => [-13.1000, 33.4000],
                    'Ntchisi Gate'     => [-13.2833, 33.8667],
                ],
            ],
            'Lilongwe' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Lilongwe City'    => [-13.9669, 33.7873],
                    'Area 1'           => [-13.9500, 33.7833],
                    'Area 2'           => [-13.9550, 33.7900],
                    'Area 3'           => [-13.9600, 33.7950],
                    'Kanengo'          => [-13.9167, 33.8167],
                    'Lumbadzi'         => [-14.0333, 33.7500],
                    'Ntandile'         => [-13.9333, 33.8333],
                    'Kalonga'          => [-13.8833, 33.8000],
                    'Mchesi'           => [-13.9833, 33.8167],
                    'Mkwinda'          => [-14.0167, 33.8500],
                    'Lingadzi'         => [-13.9417, 33.7750],
                ],
            ],
            'Mchinji' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Mchinji Boma'     => [-13.8000, 32.9000],
                    'Kamwendo'         => [-13.7500, 32.9500],
                    'Msongwe'          => [-13.8500, 32.8500],
                    'Kapiri'           => [-13.7167, 32.9833],
                    'Khami'            => [-13.8333, 32.9167],
                    'Mchinji Border'   => [-13.8167, 32.8333],
                    'Gwirizani'        => [-13.7667, 32.9333],
                ],
            ],
            'Mangochi' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Mangochi Boma'    => [-14.4782, 35.2645],
                    'Monkey Bay'       => [-14.0833, 34.9167],
                    'Malombe'          => [-14.5333, 35.3167],
                    'Cobbe Barracks'   => [-14.5000, 35.2500],
                    'Makanjira'        => [-14.6167, 35.1833],
                    'Limbwe'           => [-14.4500, 35.2833],
                    'Nkungulu'         => [-14.4167, 35.3333],
                ],
            ],
            'Nkhotakota' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Nkhotakota Boma'  => [-12.9167, 34.3000],
                    'Mbwana'           => [-12.8667, 34.2667],
                    'Dwangwa'          => [-12.5000, 34.1333],
                    'Ntchisi Gate'     => [-13.2833, 33.8667],
                    'Bua'              => [-12.7500, 34.2167],
                    'Maravi'           => [-12.9500, 34.2833],
                    'Nkhotakota Wildlife Reserve' => [-12.8500, 34.3500],
                ],
            ],
            'Ntchisi' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Ntchisi Boma'     => [-13.3500, 33.9167],
                    'Chitsanzo'        => [-13.3000, 33.9500],
                    'Ntchisi Forest'   => [-13.3833, 33.8833],
                    'Kasakula'         => [-13.3167, 33.8667],
                    'Mwadza'           => [-13.3667, 33.9500],
                    'Thukuma'          => [-13.3333, 33.9833],
                ],
            ],
            'Salima' => [
                'region' => 'Central',
                'trading_centres' => [
                    'Salima Boma'      => [-13.7500, 34.4667],
                    'Sani'             => [-13.7000, 34.4333],
                    'Chipoka'          => [-13.9833, 34.5667],
                    'Malembo'          => [-13.8333, 34.5000],
                    'Nkhotakota Gate'  => [-13.6500, 34.4500],
                    'Salima Sugar'     => [-13.7333, 34.4833],
                    'Liwonde Gate'     => [-13.6833, 34.5167],
                ],
            ],

            // ══ SOUTHERN REGION ══
            'Balaka' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Balaka Boma'      => [-14.9833, 35.0000],
                    'Liwonde'          => [-14.9167, 35.1500],
                    'Namanowa'         => [-14.9500, 35.0500],
                    'Kapiri'           => [-14.8667, 35.0333],
                    'Mlambe'           => [-15.0167, 34.9667],
                    'Utchi'            => [-14.9333, 35.0167],
                    'Balaka Border'    => [-14.9667, 35.0833],
                ],
            ],
            'Blantyre' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Blantyre City'    => [-15.7861, 35.0058],
                    'Limbe'            => [-15.8000, 35.0500],
                    'Machinjiri'       => [-15.7667, 35.0833],
                    'Ndirande'         => [-15.8167, 35.0333],
                    'Soche'            => [-15.8333, 35.0167],
                    'Chileka'          => [-15.6833, 34.9667],
                    'Chilobwe'         => [-15.8500, 35.0500],
                    'Nancholi'         => [-15.7500, 35.0333],
                    'Mandala'          => [-15.7833, 35.0167],
                    'Sunny Side'       => [-15.7917, 35.0083],
                    'Kabula'           => [-15.8167, 34.9833],
                ],
            ],
            'Chikwawa' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Chikwawa Boma'    => [-16.0333, 34.8000],
                    'Lengwe'           => [-16.0833, 34.7333],
                    'Tsangano'         => [-16.1167, 34.6833],
                    'Marka'            => [-16.1667, 34.6333],
                    'Nsanje Gate'      => [-16.2333, 34.5833],
                    'Makhwira'         => [-16.0667, 34.7667],
                    'Kandeu'           => [-16.0167, 34.8167],
                ],
            ],
            'Chiradzulu' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Chiradzulu Boma'  => [-15.7000, 35.1500],
                    'Nsinde'           => [-15.6667, 35.1833],
                    'Kadzakolo'        => [-15.7167, 35.1167],
                    'Mbera'            => [-15.6833, 35.1333],
                    'Ngombwa'          => [-15.7333, 35.1667],
                    'Lipala'           => [-15.6917, 35.1583],
                ],
            ],
            'Machinga' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Machinga Boma'    => [-15.1667, 35.3000],
                    'Liwonde'          => [-14.9167, 35.1500],
                    'Namanowa'         => [-15.0000, 35.2000],
                    'Mikoko'           => [-15.1333, 35.2833],
                    'Malingama'        => [-15.1167, 35.3167],
                    'Nayuchi'          => [-15.2500, 35.4333],
                    'Machinga Border'  => [-15.2833, 35.4667],
                ],
            ],
            'Mulanje' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Mulanje Boma'     => [-16.0333, 35.5000],
                    'Limbuli'          => [-16.0000, 35.4667],
                    'Thuchila'         => [-15.9667, 35.5167],
                    'Muloza'           => [-16.0833, 35.5500],
                    'Mulanje Mission'  => [-16.0500, 35.4833],
                    'Chambe'           => [-16.0167, 35.5333],
                    'Bondwe'           => [-15.9833, 35.5500],
                ],
            ],
            'Mwanza' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Mwanza Boma'      => [-15.6000, 34.5167],
                    'Mwanza Border'    => [-15.6333, 34.4667],
                    'Phalombe Gate'    => [-15.5667, 34.5500],
                    'Neno Gate'        => [-15.5500, 34.5333],
                    'Mwanza Hill'      => [-15.5833, 34.5000],
                    'Thambani'         => [-15.6167, 34.5333],
                ],
            ],
            'Neno' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Neno Boma'        => [-15.4500, 34.7000],
                    'Lizulu'           => [-15.4167, 34.7333],
                    'Mwami'            => [-15.4833, 34.6667],
                    'Neno Parish'      => [-15.4333, 34.7167],
                    'Luwani'           => [-15.4667, 34.6833],
                    'Mphomwa'          => [-15.4000, 34.7500],
                ],
            ],
            'Nsanje' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Nsanje Boma'      => [-16.9167, 35.2500],
                    'Nsanje Gate'      => [-16.2333, 34.5833],
                    'Marka'            => [-16.1667, 34.6333],
                    'Dzaleka'          => [-16.4000, 35.0000],
                    'Mlolo'            => [-16.5000, 35.1500],
                    'Thangwi'          => [-16.6000, 35.2000],
                    'Nyachikadza'      => [-16.7000, 35.2333],
                    'Manjinji'         => [-16.8500, 35.2667],
                ],
            ],
            'Ntcheu' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Ntcheu Boma'      => [-14.8167, 34.6333],
                    'Bilila'           => [-14.7833, 34.6667],
                    'Kandeu'           => [-14.8500, 34.6000],
                    'Mtakataka Gate'   => [-14.7500, 34.6833],
                    'Ntcheu Parish'    => [-14.8000, 34.6500],
                    'Goliati'          => [-14.7667, 34.7167],
                    'Mchere'           => [-14.8333, 34.6167],
                ],
            ],
            'Phalombe' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Phalombe Boma'    => [-15.8000, 35.6500],
                    'Mikolongwe'       => [-15.7667, 35.6833],
                    'Namiwawa'         => [-15.8333, 35.6167],
                    'Chiringa'         => [-15.8167, 35.7000],
                    'Nthalire'         => [-15.7500, 35.6667],
                    'Dziwe'            => [-15.7833, 35.6333],
                    'Phalombe Parish'  => [-15.8083, 35.6583],
                ],
            ],
            'Thyolo' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Thyolo Boma'      => [-16.0667, 35.1333],
                    'Bunga'            => [-16.0333, 35.1667],
                    'Thekerani'        => [-16.1000, 35.0833],
                    'Makwasa'          => [-16.0500, 35.1500],
                    'Misuku'           => [-16.0167, 35.1833],
                    'Thyolo Tea Estate' => [-16.0833, 35.1167],
                    'Thava'            => [-16.1167, 35.0500],
                ],
            ],
            'Zomba' => [
                'region' => 'Southern',
                'trading_centres' => [
                    'Zomba City'       => [-15.3875, 35.3188],
                    'Thondwe'          => [-15.4167, 35.3833],
                    'Domasi'           => [-15.3667, 35.3333],
                    'Mponda'           => [-15.3500, 35.3667],
                    'Naisi'            => [-15.4000, 35.3167],
                    'Ching\'ombe'      => [-15.4333, 35.2833],
                    'Zomba Plateau'    => [-15.4500, 35.3500],
                ],
            ],
        ];

        foreach ($data as $districtName => $info) {
            $district = District::updateOrCreate(
                ['name' => $districtName],
                ['region' => $info['region']],
            );

            foreach ($info['trading_centres'] as $centre => $coords) {
                TradingCentre::updateOrCreate(
                    ['district_id' => $district->id, 'name' => $centre],
                    ['latitude' => $coords[0], 'longitude' => $coords[1]],
                );
            }
        }
    }
}

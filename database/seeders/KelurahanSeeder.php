<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelurahanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelurahanByKecamatan = [
            // WRINGINANOM (3525010)
            3525010 => ['KESAMBEN WETAN', 'KESAMBEN KULON', 'BAMBE', 'JATI', 'NGEMBEH', 'WADUNG', 'WRINGINANOM'],

            // DRIYOREJO (3525020)
            3525020 => ['BAMBE', 'CANGKRING', 'DRIYOREJO', 'GADUNG', 'KARANGANDONG', 'MULUNG', 'PETIKEN', 'PLOSOGEDANG', 'SUMPUT', 'RANDEGANSARI'],

            // KEDAMEAN (3525030)
            3525030 => ['BANJARSARI', 'BUNDER', 'CANGKRING', 'KAUMAN', 'KEDAMEAN', 'MOJOASEM', 'PEGANDEN', 'SIDOHARJO', 'SUMBERREJO', 'SUROWITI', 'TANJANGAWAN', 'TANJUNGAN', 'TENGGULUNAN'],

            // MENGANTI (3525040)
            3525040 => ['ASTRO', 'BRANGSI', 'CERME KIDUL', 'MENGANTI', 'MOJOWIRYO', 'ROOMO', 'SIDOMORO', 'SINGOGALIH', 'SUKORAME', 'WEDI', 'WAYAH'],

            // CERME (3525050)
            3525050 => ['BENDUNGAN', 'BOBOH', 'CERME KIDUL', 'CERME LOR', 'GUMENO', 'KLAMPOK', 'KUPANG', 'MOROWUDI', 'NGABETAN', 'PANTENAN', 'PARE', 'PLYOSO'],

            // BENJENG (3525060)
            3525060 => ['ADILUWIH', 'AMBENG AMBENG LOR', 'BENJENG', 'BOLO', 'GEBANG', 'KALIDANDANG', 'KAMPUNGBARU', 'KAWIS ANYAR', 'MOJORUNTUT', 'SUKOMULYO', 'SUSUN', 'TANJANG', 'TLOGO PATUT'],

            // BALONGPANGGANG (3525070)
            3525070 => ['ANGGASWANGI', 'BALONGMACEKAN', 'BALONGPANGGANG', 'BRENGKOK', 'DUKUN', 'KARANGKIRING', 'KARANGREJO', 'KEMBANGAN', 'KRIKILAN', 'METATU', 'NGARGOSARI', 'PENDULANGAN', 'SIDOMULYO'],

            // DUDUKSAMPEYAN (3525080)
            3525080 => ['BLONGKO', 'DAGAN', 'DUKUHSEMBUNG', 'DUDUKSAMPEYAN', 'GOSUN', 'GUNUNGTEGUH', 'KERTOSONO', 'LUMPUR', 'PONGANGAN', 'SIDOGEDUNGBATU', 'WRINGINPITU'],

            // SIDAYU (3525130)
            3525130 => ['ASEMPAPAK', 'BANYUTENGAH', 'BANYUURIP', 'BEDILAN', 'KRAMAT', 'PUCUK', 'RACI TENGAH', 'RACI KULON', 'SARANGAN', 'SIDAYU', 'SIDOMUKTI', 'SIMOANGINANGIN', 'SUKOANYAR', 'SUKOWATI', 'TLOGOPATUT'],

            // DUKUN (3525140)
            3525140 => ['BANJAR KEMUNING', 'BANYUAJUH', 'DUKUN', 'JATIREJO', 'KARANGCANGKRING', 'KRAMAT', 'MADURESO', 'MOROBAKUNG', 'MORODEMAK', 'SEKAPUK', 'SEMBUNGREJO', 'WATES', 'WADAK'],

            // PANCENG (3525150)
            3525150 => ['DALEGAN', 'DUKUH DOWO', 'GONDANG', 'KARANGREJO', 'KROMAN', 'PANCENG', 'PRUPUH', 'RAMBAN', 'SIDOJANGKUNG', 'SUMENGKO', 'SUMOROTO', 'TLOGOAGUNG'],

            // UJUNGPANGKAH (3525160)
            3525160 => ['BANYUWANGI', 'BAYUR', 'GOSARI', 'KARANGKEMBANG', 'KARANGKIRING', 'PANGKAHKULON', 'PANGKAHJENE', 'PANGKAHWETAN', 'PASAR BUNDER', 'PULOPANCIKAN', 'ROMOKALISARI', 'SEGOROMADU', 'SOCOREJO', 'TANJANGKEMANTREN', 'WADAK KIDUL'],

            // SANGKAPURA (3525170)
            3525170 => ['AENG SAREH', 'BRINGSANG', 'BULU', 'KOMBANG', 'LOBUK', 'PAGERUNGAN BESAR', 'PAGERUNGAN KECIL', 'PAGERUNGAN REJO', 'POLAGAN', 'SANGKAPURA', 'SAPEKEN', 'SASIRINGAN'],

            // TAMBAK (3525180)
            3525180 => ['BANARAN', 'KARANGANYAR', 'KARANGGEGER', 'KRAMAT', 'MASANGAN KULON', 'MASANGAN WETAN', 'MOJOPUROGEDE', 'REJENI', 'SUCI', 'SUMBEREJO', 'TAMBAK', 'TAMBAKREJO'],
        ];

        foreach ($kelurahanByKecamatan as $kecamatanId => $kelurahanNames) {
            $counter = 1;
            foreach ($kelurahanNames as $name) {
                $id = $kecamatanId . str_pad($counter, 3, '0', STR_PAD_LEFT);

                DB::table('kelurahan')->updateOrInsert(
                    ['id' => $id],
                    [
                        'id' => $id,
                        'kecamatan_id' => $kecamatanId,
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
                $counter++;
            }
        }
    }
}

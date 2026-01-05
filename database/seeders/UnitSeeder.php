<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\UnitType;
use App\Models\City;
use App\Models\User;

class UnitSeeder extends Seeder
{
    private $nationalCodeBase = 4400000000;

    public function run(): void
    {
        $zanjan = City::where('name', 'زنجان')->first();
        $abhar = City::where('name', 'ابهر')->first();
        $ministry = Unit::where('name', 'وزارت بهداشت')->first();

        // ۱. دانشگاه و معاونت (شهر زنجان)
        $uni = $this->addUnit('دانشگاه علوم پزشکی زنجان', 'دانشگاه علوم پزشکی', $ministry->id, $zanjan->id);
        $mBehdasht = $this->addUnit('معاونت بهداشت دانشگاه علوم پزشکی زنجان', 'معاونت بهداشت', $uni->id, $zanjan->id);

        // ۲. شبکه ابهر (شهر ابهر)
        $setadAbhar = $this->addUnit('ستاد شبکه بهداشت و درمان ابهر', 'ستاد شبکه بهداشت و درمان', $mBehdasht->id, $abhar->id);
        
        // ۳. حوزه مدیریت و امور عمومی
        $hazeModiriat = $this->addUnit('حوزه مدیریت شبکه ابهر', 'حوزه مدیریت', $setadAbhar->id, $abhar->id);
        $omurOmumi = $this->addUnit('اداره امور عمومی شبکه ابهر', 'واحد اداری', $setadAbhar->id, $abhar->id);

        // ۴. زیرمجموعه‌های امور عمومی
        $this->addUnit('کارگزینی ستاد شبکه', 'کارگزینی', $omurOmumi->id, $abhar->id);
        $this->addUnit('خدمات و نقلیه', 'واحد اداری', $omurOmumi->id, $abhar->id);
        $this->addUnit('دبیرخانه', 'واحد اداری', $omurOmumi->id, $abhar->id);
        $this->addUnit('دفتر فنی', 'واحد اداری', $omurOmumi->id, $abhar->id);

        // ۵. زیرمجموعه‌های حوزه مدیریت
        $this->addUnit('دفتر مدیریت', 'واحد اداری', $hazeModiriat->id, $abhar->id);
        $this->addUnit('روابط عمومی', 'واحد اداری', $hazeModiriat->id, $abhar->id);
        $this->addUnit('نظارت بر درمان', 'واحد اداری', $hazeModiriat->id, $abhar->id);
        $this->addUnit('کنترل مواد غذایی', 'واحد اداری', $hazeModiriat->id, $abhar->id);
        $this->addUnit('حراست', 'واحد اداری', $hazeModiriat->id, $abhar->id);
        $omurMali = $this->addUnit('اداره امور مالی', 'امور مالی', $hazeModiriat->id, $abhar->id);

        // ۶. زیرمجموعه مالی
        $this->addUnit('اسناد پزشکی', 'امور مالی', $omurMali->id, $abhar->id);
        $this->addUnit('اموال', 'امور مالی', $omurMali->id, $abhar->id);
        $this->addUnit('حسابداری', 'امور مالی', $omurMali->id, $abhar->id);

        // ۷. مرکز بهداشت شهرستان و واحدهای فنی
        $mBehdashtShahrestan = $this->addUnit('مرکز بهداشت شهرستان ابهر', 'مرکز بهداشت شهرستان', $setadAbhar->id, $abhar->id);
        $vFanni = $this->addUnit('واحدهای فنی مرکز بهداشت شهرستان', 'واحد فنی مرکز بهداشت', $mBehdashtShahrestan->id, $abhar->id);
        $vMohiti = $this->addUnit('واحدهای محیطی مرکز بهداشت شهرستان', 'واحد محیطی مرکز بهداشت', $mBehdashtShahrestan->id, $abhar->id);

        // ۸. لیست واحدهای فنی
        $fanniItems = ['سلامت محیط', 'سلامت حرفه ای', 'مبارزه با بیماریهای واگیر', 'مبارزه با بیماریهای غیرواگیر', 'آموزش سلامت', 'توسعه شبکه', 'آموزشگاه بهورزی', 'فناوری اطلاعات', 'مدیریت بحران و بلایا', 'امور دارویی', 'سلامت روان', 'تغذیه', 'سلامت خانواده', 'امور آزمایشگاه ها', 'تجهیزات پزشکی', 'سلامت دهان و دندان'];
        foreach ($fanniItems as $item) {
            $this->addUnit("واحد $item", 'واحد فنی مرکز بهداشت', $vFanni->id, $abhar->id);
        }

        // ۹. مراکز خدمات جامع سلامت (شهری)
        $uCenters = ['اعلایی', 'هفده شهریور', 'شماره 5', 'هیدج'];
        foreach ($uCenters as $name) {
            $c = $this->addUnit("مرکز $name", 'مرکز خدمات جامع سلامت شهری', $vMohiti->id, $abhar->id);
            $this->addUnit("پایگاه سلامت ضمیمه $name", 'پایگاه سلامت ضمیمه', $c->id, $abhar->id);
            if($name == 'هفده شهریور') {
                $this->addUnit("پایگاه سلامت غیر ضمیمه 2 $name", 'پایگاه سلامت غیر ضمیمه', $c->id, $abhar->id);
                $this->addUnit("پایگاه سلامت غیر ضمیمه 3 $name", 'پایگاه سلامت غیر ضمیمه', $c->id, $abhar->id);
            }
            if($name == 'هیدج') $this->addUnit("پایگاه سلامت غیر ضمیمه هیدج", 'پایگاه سلامت غیر ضمیمه', $c->id, $abhar->id);
            if($name == 'شماره 5') {} // فقط ضمیمه طبق لیست شما
        }

        // ۱۰. مراکز روستایی و خانه‌های بهداشت
        $rCenters = [
            'قروه' => ['قروه', 'قمچ آباد', 'توده بین', 'حصار قاجار'],
            'الگزیر' => ['الگزیر', 'ارهان'],
            'درسجین' => ['ازناب', 'خلیفه حصار', 'درسجین'],
            'دولت آباد' => ['آغور', 'دولت آباد', 'ینگی کند', 'چنگ الماس', 'ایوانک'],
            'عباس آباد' => ['عباس آباد', 'امیر بستاق', 'چشین', 'خوشنام', 'نایجوک', 'زره باش', 'قزلجه', 'قفس آباد'],
            'عمید آباد' => ['عمید آباد', 'جداقیه']
        ];
        foreach ($rCenters as $cName => $houses) {
            $c = $this->addUnit("مرکز $cName", 'مرکز خدمات جامع سلامت روستایی', $vMohiti->id, $abhar->id);
            foreach ($houses as $h) $this->addUnit("خانه بهداشت $h", 'خانه بهداشت', $c->id, $abhar->id);
        }

        // ۱۱. مراکز شهری روستایی
        $urCenters = [
            'حسین آباد' => ['اسپاس', 'فنوش آباد', 'میموندره', 'کینه ورس', 'چالچوق', 'کوی نیک'],
            'شناط' => ['قارلوق', 'مرشون'],
            'شریف آباد' => ['نورین'],
            'صائین قلعه' => ['پیر سقا', 'چرگر', 'خراسانلو', 'داشبلاغ', 'سروان جهان', 'کبود چشمه', 'کوه زین', 'مهستان']
        ];
        foreach ($urCenters as $cName => $houses) {
            $c = $this->addUnit("مرکز $cName", 'مرکز خدمات جامع سلامت شهری روستایی', $vMohiti->id, $abhar->id);
            $this->addUnit("پایگاه سلامت ضمیمه $cName", 'پایگاه سلامت ضمیمه', $c->id, $abhar->id);
            if($cName == 'شناط') {
                $this->addUnit("پایگاه سلامت غیر ضمیمه 2 شناط", 'پایگاه سلامت غیر ضمیمه', $c->id, $abhar->id);
                $this->addUnit("پایگاه سلامت غیر ضمیمه 3 شناط", 'پایگاه سلامت غیر ضمیمه', $c->id, $abhar->id);
            }
            if($cName == 'صائین قلعه') $this->addUnit("پایگاه سلامت غیر ضمیمه صائین قلعه", 'پایگاه سلامت غیر ضمیمه', $c->id, $abhar->id);
            if($cName == 'شریف آباد') $this->addUnit("پایگاه سلامت غیر ضمیمه شریف آباد", 'پایگاه سلامت غیر ضمیمه', $c->id, $abhar->id);
            
            foreach ($houses as $h) $this->addUnit("خانه بهداشت $h", 'خانه بهداشت', $c->id, $abhar->id);
        }
    }

    private function addUnit($name, $typeName, $parentId, $cityId) {
        $type = UnitType::where('title', $typeName)->first();
        $unit = Unit::firstOrCreate([
            'name' => $name,
            'unit_type_id' => $type->id,
            'parent_id' => $parentId,
            'city_id' => $cityId,
            'is_active' => 1
        ]);

        $this->nationalCodeBase++;
        User::firstOrCreate(
            ['national_code' => (string)$this->nationalCodeBase],
            [
                'full_name' => "مسئول $name",
                'unit_id' => $unit->id,
                'password' => bcrypt('12345678'),
                'is_active' => 1
            ]
        );
        return $unit;
    }
}
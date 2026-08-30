# Awamu ya 3 — Ratiba, Ripoti, na Dashibodi ya Maendeleo

Hii ni implementation ya `prd.md` §5.3, ikijengwa juu ya msingi wa Awamu ya 1
uliopo tayari (`BelongsToDepartment` trait, `DepartmentScope`, Policy pattern,
`EnsureDepartmentAccess` middleware). Copy-paste faili hizi juu ya folder yako
`idara-management/` iliyopo (zinaandikwa upya: `app/Models/Department.php`,
`app/Providers/AppServiceProvider.php`, `routes/web.php`, `routes/console.php`,
`composer.json` — zote nyingine ni mpya).

## Hatua za Usanidi (Setup)

```bash
composer require barryvdh/laravel-dompdf
php artisan vendor:publish --tag=dompdf_config   # hiari, kama unataka kubadilisha paper size n.k.

php artisan migrate

# jaribu kuzalisha ripoti mara moja kwa mkono:
php artisan report:generate --year=2026
```

Hakikisha cron ya server inapiga `php artisan schedule:run` kila dakika
(kama stacks.md §5 inavyosema) ili `report:generate` ya mwaka iweze kujiendesha
kiotomatiki tarehe 31 Desemba.

## Nini Kimeongezwa

| Faili | Kazi yake |
|---|---|
| `database/migrations/2025_03_01_*` | Majedwali matatu mapya: `annual_schedules`, `activity_logs`, `reports` |
| `app/Models/AnnualSchedule.php`, `ActivityLog.php`, `Report.php` | Yanatumia trait ile ile `BelongsToDepartment` (Global Scope) inayotumiwa na moduli za Awamu ya 2/3 zilizopangwa kwenye `architecture.md` §3 |
| `app/Models/Department.php` **(imeandikwa upya)** | Imeongezewa relations: `annualSchedules()`, `activityLogs()`, `reports()` |
| `app/Policies/AnnualSchedulePolicy.php`, `ActivityLogPolicy.php`, `ReportPolicy.php` | Kiongozi wa idara husika (au Admin) pekee ndiye anaunda/anahariri/anafuta |
| `app/Http/Requests/Store*`, `Update*` | Validation + `authorize()` sawa na `StoreDepartmentRequest` pattern iliyopo |
| `app/Http/Controllers/AnnualScheduleController.php` | CRUD ya ratiba, scoped kwa `{department}` |
| `app/Http/Controllers/ActivityLogController.php` | Kurekodi shughuli; ikirekodiwa dhidi ya ratiba, inaweka kipengele husika `completed` kiotomatiki |
| `app/Http/Controllers/DepartmentProgressController.php` | Dashibodi ya maendeleo: asilimia ya ratiba iliyokamilika, shughuli za hivi karibuni, ripoti ya mwisho |
| `app/Console/Commands/GenerateDepartmentReports.php` | `report:generate` — inazalisha PDF ya kila idara kwa DomPDF, inahifadhi kwenye `storage/app/private/reports/` na kuandika rekodi kwenye jedwali `reports` |
| `app/Providers/AppServiceProvider.php` **(imeandikwa upya)** | Imeongezewa `Gate::policy()` kwa Policy tatu mpya |
| `routes/web.php` **(imeandikwa upya)** | Njia mpya: `schedules.index/store/update/destroy`, `activity-logs.store`, `departments.progress` — zote ndani ya `department.access` middleware iliyopo |
| `routes/console.php` **(imeandikwa upya)** | `Schedule::command('report:generate --period=yearly')->yearlyOn(...)` |
| `composer.json` **(imeandikwa upya)** | Imeongezewa `barryvdh/laravel-dompdf` kama ilivyopangwa kwenye `stacks.md` §7 |
| `resources/views/reports/pdf.blade.php` | Template ya PDF ya ripoti |
| `resources/views/departments/progress.blade.php` | UI ya dashibodi ya maendeleo |
| `resources/views/schedules/index.blade.php` | UI ya kuongeza/kuona ratiba |
| `tests/Feature/Phase3ScopingTest.php` | Inathibitisha department-scoping kwa ratiba, sawa na `DepartmentScopingTest.php` iliyopo |

## Vitu vya Kufanya Kabla ya Production

1. **`department_transactions` (Awamu ya 2) bado haijajengwa** kwenye codebase
   hii — `report:generate` kwa sasa inatumia `annual_schedules` +
   `activity_logs` pekee. Ukijenga jedwali/model ya miamala baadaye, ongeza
   query yake ndani ya `GenerateDepartmentReports::generateForDepartment()`
   na uonyeshe kwenye `reports/pdf.blade.php`.
2. Ongeza kiungo cha "Pakua Ripoti" (download link) kwenye
   `departments/progress.blade.php` kinachoelekeza kwenye route ya
   `Storage::disk('local')->download($latestReport->file_path)` — nimeacha
   hii nje kwa makusudi mpaka uamue kama utahitaji ukaguzi wa ziada wa ruhusa
   kabla ya kupakua faili la PDF.
3. Fikiria menu link kwenye `resources/views/layouts/app.blade.php` kuelekea
   `route('schedules.index', $department)` na `route('departments.progress', $department)`
   kwa kila idara mtumiaji anamo — kwa sasa zinafikika kwa URL moja kwa moja tu.
4. Piga `php artisan test` baada ya kuweka faili hizi — `Phase3ScopingTest`
   inatumia `AnnualScheduleFactory` mpya, hivyo hakikisha imeingia kwenye
   `database/factories/`.

<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\View::composer('layouts.app', function ($view) {
            if (! auth()->check()) {
                $view->with([
                    'systemNotifications' => collect(),
                    'notificationsCount' => 0,
                ]);

                return;
            }

            try {
                // Collect admissions (new or updated in last 48 hours)
                $recentStudents = \App\Models\Student::with('schoolClass')
                    ->where('updated_at', '>=', now()->subHours(48))
                    ->latest('updated_at')
                    ->limit(5)
                    ->get()
                    ->map(function ($s) {
                        $isNew = $s->created_at->diffInMinutes($s->updated_at) < 5;

                        return [
                            'type' => 'student',
                            'title' => ($isNew ? 'New Admission: ' : 'Student Updated: ').$s->name,
                            'subtitle' => 'Adm: '.$s->admission_number.' ('.($s->schoolClass?->name ?? 'Class').')',
                            'url' => route('students.show', $s->id),
                            'time' => $s->updated_at->diffForHumans(),
                            'icon' => 'bi-mortarboard-fill',
                            'color' => 'text-primary',
                            'timestamp' => $s->updated_at,
                        ];
                    });

                // Collect fee vouchers (new or updated in last 48 hours)
                $recentInvoices = \App\Models\FeeInvoice::with('student')
                    ->where('updated_at', '>=', now()->subHours(48))
                    ->latest('updated_at')
                    ->limit(5)
                    ->get()
                    ->map(function ($inv) {
                        $isNew = $inv->created_at->diffInMinutes($inv->updated_at) < 5;
                        $studentName = $inv->student ? $inv->student->name : 'Student';

                        return [
                            'type' => 'invoice',
                            'title' => ($isNew ? 'Fee Voucher Issued: ' : 'Fee Voucher Updated: ').$inv->invoice_number,
                            'subtitle' => $studentName.' - '.number_format((float) $inv->total_amount, 2).' ('.ucfirst($inv->status).')',
                            'url' => route('accountant.show', $inv->id),
                            'time' => $inv->updated_at->diffForHumans(),
                            'icon' => 'bi-receipt',
                            'color' => 'text-success',
                            'timestamp' => $inv->updated_at,
                        ];
                    });

                $allNotifications = $recentStudents->concat($recentInvoices)->sortByDesc('timestamp')->values();

                $view->with([
                    'systemNotifications' => $allNotifications,
                    'notificationsCount' => $allNotifications->count(),
                ]);
            } catch (\Throwable $e) {
                $view->with([
                    'systemNotifications' => collect(),
                    'notificationsCount' => 0,
                ]);
            }
        });
    }
}

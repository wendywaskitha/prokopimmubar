<div class="pt-6 pb-12 text-center text-xs text-gray-500">
    <?php
        $appName = config('app.name');
        $appSubtitle = config('app.company_name', '');
        
        try {
            if (class_exists(\App\Models\Setting::class) && \Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $appName = \App\Models\Setting::get('app_name', config('app.name'));
                $appSubtitle = \App\Models\Setting::get('app_subtitle', config('app.company_name'));
            }
        } catch (\Exception $e) {
            // Silently fail if settings table is not accessible
        }
    ?>
    &copy;<?php echo e(date('Y')); ?> <?php echo e($appName); ?><br><?php echo e($appSubtitle); ?>

    <div class="mt-2">Crafted by Arsicom</div>
</div>
<?php /**PATH D:\ARSICOM\PROYEK CMS\liwumokesanews\resources\views/panels/footer.blade.php ENDPATH**/ ?>
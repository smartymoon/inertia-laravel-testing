<?php


namespace Smartymoon\Inertia;


trait InertiaSetup
{
    public function setUpInertia()
    {
        $inertiaVersion = '';
        if (config('app.asset_url')) {
            $inertiaVersion =  md5(config('app.asset_url'));
        } else if (file_exists($manifest = public_path('mix-manifest.json'))) {
            $inertiaVersion = md5_file($manifest);
        }
        $this->withHeaders([
            'X-Inertia' => true,
            'X-Inertia-Version' => $inertiaVersion
        ]);
    }
}
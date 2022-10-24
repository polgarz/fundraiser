<?php
return [
    //admin
    'admin' => 'site/admin',

    // felhasznalok
    'login' => 'user/login',

    // kampanyok
    'kampany/kampanyod-szerkesztese' => 'campaign/update-ambassador-campaign',
    'kampany/kampanyod-torlese' => 'campaign/delete-ambassador-campaign',
    'kampany/<slug>' => 'campaign/details',
    'kampany/<slug>/jelentkezes-nagykovetnek' => 'campaign/apply',
    'kampany/<campaign_slug>/<slug>' => 'campaign/ambassador',

    // sitemap
    ['pattern' => 'sitemap', 'route' => 'sitemap/default/index', 'suffix' => '.xml'],
];

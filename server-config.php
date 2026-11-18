<?php
// Hosting configuration only. Never place passwords or submitted personal data here.
// On xneelo, set this to a confirmed PRIVATE account directory outside the web root,
// e.g. an account-specific directory under /usr/home/<account>. Do not guess the path.
return [
    'enabled' => false,
    'private_dir' => getenv('TCF_PRIVATE_DIR') ?: '',
    'recipient' => 'admin@thechorefacilitator.co.za',
    'sender' => 'admin@thechorefacilitator.co.za',
    'origins' => ['https://thechorefacilitator.co.za', 'https://www.thechorefacilitator.co.za'],
];

<?php

namespace Tests\Unit\Backend\AflCallbacks;

use App\Models\AflInstallations;
use App\Models\AflLicenses;
use App\Models\AflProducts;
use Tests\TestCase;

class LicenseVerifyControllerTest extends TestCase
{
    protected function scriptSignature($root_url, $client_email, $license_code, $product_id)
    {
        $ROOT_URL = url('/');
        $root_ips_array = gethostbynamel(aflGetRawDomain($ROOT_URL));

        $license_signature = hash('sha256', gmdate('Y-m-d').$root_url.$client_email.$license_code.$product_id.implode('', $root_ips_array));

        return $license_signature;
    }

    public function test_licenseVerify_afterInstallSuccessfully_shouldRecieveLicenseOk()
    {
        $root_url = 'https://www.faveo.com';
        $license_code = 'JDI0MLOX63U8WS90';
        $client_email = '';
        $product_id = 25;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $data = [
            'product_id' => 25,
            'license_code' => 'JDI0MLOX63U8WS90',
            'root_url' => 'https://www.faveo.com',
            'installation_hash' => '3ca1710b87ca6250c7aa847d92a2a8488fe08a18c2967c037cc0ed988d77435a',
            'license_signature' => $license_signature,
            'refer' => 'https://www.faveo.com',
        ];

        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertStatus(200);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification-text', 'Requested product not found');
    }

    public function test_licenseVerify_afterInstallWithoutProduct_shouldRecieveProductNotFound()
    {
        $data = [
            'product_id' => 26,
            'license_code' => 'JDI0MLOX63U8WS90',
            'root_url' => 'https://www.faveo.com',
            'installation_hash' => '3ca1710b87ca6250c7aa847d92a2a8488fe08a18c2967c037cc0ed988d77435a',
            'license_signature' => '65483ef1960805fedaadefb33bda45ecd1905400a81b083c80345185bf97f163',
            'refer' => 'https://www.faveo.com',
        ];

        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification-text', 'Requested product not found');
    }

    public function test_licenseVerify_afterInstallProductInactive_shouldRecieveProductInactive()
    {
        $product = AflProducts::factory()->create(['product_id' => rand(1000,9999), 'product_sku' => \Str::random(10), 'product_status' => 0]);
        AflLicenses::factory()->create(['product_id' => $product->product_id, 'license_code' => 'JDI0MLOX63U8WS33']);
        $root_url = 'https://www.faveo.com';
        $license_code = 'JDI0MLOX63U8WS33';
        $client_email = '';
        $product_id = $product->product_id;
        $installation_hash = hash('sha256', $root_url.$client_email.$license_code);
        $data = [
            'product_id' => $product->product_id,
            'license_code' => 'JDI0MLOX63U8WS33',
            'root_url' => 'https://www.faveo.com',
            'installation_hash' => $installation_hash,
            'license_signature' => '65483ef1960805fedaadefb33bda45ecd1905400a81b083c80345185bf97f163',
            'refer' => 'https://www.faveo.com',
        ];

        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_inactive');
        $response->assertHeader('notification-text', 'Product Helpdesk Product 2 is inactive');
        AflProducts::where('product_id', $product->product_id)->delete();
        AflLicenses::where('product_id', $product->product_id)->delete();
    }

    public function test_licenseVerify_afterInstallWithoutLicenseCode_shouldRecieveLicenseCodeNotFound()
    {
        $data = [
            'product_id' => 25,
            'license_code' => 'JSOPCKUIPOLD890D',
            'root_url' => 'https://www.faveo.com',
            'installation_hash' => '3659bb5ed489164e59c42969bea1a03af1016365b2074f60cfdea5fea9fd16f6',
            'license_signature' => '65483ef1960805fedaadefb33bda45ecd1905400a81b083c80345185bf97f163',
            'refer' => 'https://www.faveo.com',
        ];

        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification-text', 'Requested product not found');
    }

    public function test_licenseVerify_afterInstallWithAInvalidSignature_shouldRecieveInvalidScriptSignature()
    {
        $data = [
            'product_id' => 25,
            'license_code' => 'JDI0MLOX63U8WS90',
            'root_url' => 'https://www.faveo.com',
            'installation_hash' => '3ca1710b87ca6250c7aa847d92a2a8488fe08a18c2967c037cc0ed988d77435a',
            'license_signature' => '65483ef1960805fedaadefb33bda45ecd1905400a81b083c80345185bf97f1633',
            'refer' => 'https://www.faveo.com',
        ];

        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification-text', 'Requested product not found');
    }

    public function test_licenseVerify_afterInstallWithLicenseStatus0_shouldRecieveLicenseCancelled()
    {
        AflLicenses::factory()->create(['product_id' => 25, 'license_code' => 'JDI0MLOX63U8WS40', 'license_status' => 0]);
        $root_url = 'https://www.faveohelpdesk.com';
        $license_code = 'JDI0MLOX63U8WS40';
        $client_email = '';
        $product_id = 25;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $data = [
            'product_id' => $product_id,
            'license_code' => $license_code,
            'root_url' => $root_url,
            'installation_hash' => 'c1b1c54be267f586250bff56647dc1de0789ce90748f42cbf0e61091f1ae4a22',
            'license_signature' => $license_signature,
            'refer' => 'https://www.faveohelpdesk.com',
        ];
        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        AflLicenses::where('license_code', 'JDI0MLOX63U8WS40')->delete();
    }

    public function test_licenseVerify_afterInstallWithLicenseStatus2_shouldRecieveLicenseSuspended()
    {
        AflLicenses::factory()->create(['product_id' => 25, 'license_code' => 'JDI0MLOX63U8WS40', 'license_status' => 2]);
        $root_url = 'https://www.faveohelpdesk.com';
        $license_code = 'JDI0MLOX63U8WS40';
        $client_email = '';
        $product_id = 25;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $data = [
            'product_id' => $product_id,
            'license_code' => $license_code,
            'root_url' => $root_url,
            'installation_hash' => 'c1b1c54be267f586250bff56647dc1de0789ce90748f42cbf0e61091f1ae4a22',
            'license_signature' => $license_signature,
            'refer' => 'https://www.faveohelpdesk.com',
        ];
        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification-text', 'Requested product not found');
        AflLicenses::where('license_code', 'JDI0MLOX63U8WS40')->delete();
    }

    public function test_licenseVerify_afterInstallWithLicenseExpired_shouldRecieveLicenseExpired()
    {
        AflLicenses::factory()->create(['product_id' => 25, 'license_code' => 'JDI0MLOX63U8WS40', 'license_expire_date' => '2020-09-20']);
        $root_url = 'https://www.faveohelpdesk.com';
        $license_code = 'JDI0MLOX63U8WS40';
        $client_email = '';
        $product_id = 25;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $data = [
            'product_id' => $product_id,
            'license_code' => $license_code,
            'root_url' => $root_url,
            'installation_hash' => 'c1b1c54be267f586250bff56647dc1de0789ce90748f42cbf0e61091f1ae4a22',
            'license_signature' => $license_signature,
            'refer' => 'https://www.faveohelpdesk.com',
        ];
        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification-text', 'Requested product not found');
        AflLicenses::where('license_code', 'JDI0MLOX63U8WS40')->delete();
    }

    public function test_licenseVerify_afterInstallWithInvalidIp_shouldRecieveInvalidIp()
    {
        AflLicenses::factory()->create(['product_id' => 25, 'license_code' => 'JDI0MLOX63U8WS40', 'license_ip' => '108.90.09.90']);
        $root_url = 'https://www.faveohelpdesk.com';
        $license_code = 'JDI0MLOX63U8WS40';
        $client_email = '';
        $product_id = 25;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $data = [
            'product_id' => $product_id,
            'license_code' => $license_code,
            'root_url' => $root_url,
            'installation_hash' => 'c1b1c54be267f586250bff56647dc1de0789ce90748f42cbf0e61091f1ae4a22',
            'license_signature' => $license_signature,
            'refer' => 'https://www.faveohelpdesk.com',
        ];
        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        AflLicenses::where('license_code', 'JDI0MLOX63U8WS40')->delete();
    }

    public function test_licenseVerify_afterInstallWithInvalidDomain_shouldRecieveInvalidDomain()
    {
        AflLicenses::factory()->create(['product_id' => 25, 'license_code' => 'JDI0MLOX63U8WS40', 'license_domain' => 'license.com']);
        $root_url = 'https://www.faveohelpdesk.com';
        $license_code = 'JDI0MLOX63U8WS40';
        $client_email = '';
        $product_id = 25;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $data = [
            'product_id' => $product_id,
            'license_code' => $license_code,
            'root_url' => $root_url,
            'installation_hash' => 'c1b1c54be267f586250bff56647dc1de0789ce90748f42cbf0e61091f1ae4a22',
            'license_signature' => $license_signature,
            'refer' => 'https://www.faveohelpdesk.com',
        ];
        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification_text', 'Requested product not found');
        AflLicenses::where('license_code', 'JDI0MLOX63U8WS40')->delete();
    }

    public function test_licenseVerify_afterInstallWithoutRealDomain_shouldRecieveInvalidDomain()
    {
        AflLicenses::factory()->create(['product_id' => 25, 'license_code' => 'JDI0MLOX63U8WS40', 'license_require_domain' => 1]);
        $root_url = 'http://www.lslsllsls.com';
        $license_code = 'JDI0MLOX63U8WS40';
        $client_email = '';
        $product_id = 25;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $data = [
            'product_id' => $product_id,
            'license_code' => $license_code,
            'root_url' => $root_url,
            'installation_hash' => '7f5d00a82030b9045d792cdcb4d366d2e5e95a2df460ed5702ecbd7589953522',
            'license_signature' => $license_signature,
            'refer' => 'http://www.lslsllsls.com',
        ];
        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification_text', 'Requested product not found');
        AflLicenses::where('license_code', 'JDI0MLOX63U8WS40')->delete();
    }

    public function test_licenseVerify_afterInstallWithSameDetails_shouldRecieveDomainInUse()
    {
        AflLicenses::factory()->create(['product_id' => 25, 'license_code' => 'JDI0MLOX63U8WS40']);
        $root_url = 'https://www.faveo.com';
        $license_code = 'JDI0MLOX63U8WS40';
        $client_email = '';
        $product_id = 25;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $installation_hash = hash('sha256', $root_url.$client_email.$license_code);
        $data = [
            'product_id' => $product_id,
            'license_code' => $license_code,
            'root_url' => $root_url,
            'installation_hash' => $installation_hash,
            'license_signature' => $license_signature,
            'refer' => 'https://www.faveo.com',
        ];
        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_product_not_found');
        $response->assertHeader('notification_text', 'Requested product not found');
        AflLicenses::where('license_code', 'JDI0MLOX63U8WS40')->delete();
    }

    public function test_licenseVerify_afterInstallNotFound_shouldRecieve()
    {
        $product = AflProducts::factory()->create(['product_id' => rand(1000,9999), 'product_sku' => 'LICEN-AGAIN']);
        AflLicenses::factory()->create(['product_id' => $product->product_id, 'license_code' => 'JDI0MLOX63U8WS70', 'license_limit' => 1]);
        AflInstallations::factory()->create(['product_id' => $product->product_id, 'license_code' => 'JDI0MLOX63U8WS70']);
        $root_url = 'https://www.faveo.com';
        $license_code = 'JDI0MLOX63U8WS70';
        $client_email = '';
        $product_id = $product->product_id;
        $license_signature = $this->scriptSignature($root_url, $license_code, $client_email, $product_id);
        $installation_hash = hash('sha256', $root_url.$client_email.$license_code);
        $data = [
            'product_id' => $product_id,
            'license_code' => $license_code,
            'root_url' => 'https://www.faveo.com',
            'installation_hash' => $installation_hash,
            'license_signature' => $license_signature,
            'refer' => 'https://www.faveo.com',
        ];
        $response = $this->json('POST', url('api/licenseVerify'), $data);
        $response->assertHeader('notification_case', 'notification_installation_not_found');
        AflProducts::where('product_id', $product->product_id)->delete();
        AflLicenses::where('product_id', $product->product_id)->delete();
        AflInstallations::where('product_id', $product->product_id)->delete();
        AflProducts::where('product_id', 25)->delete();
        AflLicenses::where('product_id', 25)->delete();
        AflInstallations::where('product_id', 25)->delete();
    }
}

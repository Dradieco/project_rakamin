<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class user_test extends TestCase {
    /**
     * Set $strictRequestErrorCheck false, because the following error occurs if true
     *
     * 1) Example_test::test_users_get_format_csv
     * RuntimeException: Array to string conversion on line 373 in file .../application/libraries/Format.php
     *
     * @var bool
     */
    // protected $strictRequestErrorCheck = false;

    // public function test_index () {
    //     // $this->request->setHeader('Accept', 'application/csv');

    //     try {
    //         $output = $this->request('GET', 'api/user');
	// 	} catch (CIPHPUnitTestExitException $e) {
	// 		$output = ob_get_clean();
	// 	}

    //     $response = json_decode($output);
        
    //     // $this->assertArrayHasKey('status', $output);
    //     $this->assertEquals( "Data User Found", $response->message);
    //     $this->assertResponseCode(200);
    //     // $this->assertEquals(200, $this->request('GET', 'api/user?a')->getStatusCode());

    //     // $value = [
    //     //     "status"=>true,
    //     //     "message"=>"Data Found",
    //     //     "data"=>["id"=>"1","user_id"=>"USR-001","username"=>"Ricky Raven","phonenumber"=>"081905377261","auth_key"=>"1234567890","ts_insert"=>"2022-12-17 03=>07=>11","status"=>"1"]
    //     // ];
    //     // var_dump($output);
        
    //     // $this->assertStringContainsString('status, message, data', $output);
    //     // $this->assertEquals( 'status, message, data', $output);
    //     // $this->assertEquals( '[{"id":"1","user_id":"USR-001","username":"Ricky Raven","phonenumber":"081905377261","auth_key":"1234567890","ts_insert":"2022-12-17 03:07:11","status":"1"},{"id":"10","user_id":"USR-102","username":"Giovanni","phonenumber":"0987621312","auth_key":"iElXTchBKQ","ts_insert":"2022-12-17 11:58:10","status":"1"},{"id":"11","user_id":"USR-263","username":"Rico","phonenumber":"098762987","auth_key":"BO5SN0x6Ki","ts_insert":"2022-12-17 11:59:04","status":"1"}]', $output);
    //     // $output->assertResponseCode(200);
    //     // $this->assertJson($value);
    // }

    public function test_user_post () {
        try {
			$outputs = $this->request(
				'POST', 'api/user', ['username' => "test_user", 'phonenumber' => 123]
			);
		} catch (CIPHPUnitTestExitException $e) {
			$outputs = ob_get_clean();
		}
		$response = json_decode($outputs);

        // echo $response;

		$this->assertTrue($response->status);
		// $this->assertEquals('{"status":false,"message":"This Phone Number has been Registered"}{"status":false,"message":"This Phone Number has been Registered"}', $output);
		$this->assertResponseCode(406);
    }
}
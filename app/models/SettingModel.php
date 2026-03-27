<?php
class SettingModel extends Database {
    
    public function getSettings() {
        $db = $this->getConnection();
        $result = $db->query("SELECT * FROM shop_settings WHERE id = 1");
        return $result->fetch_assoc();
    }

    public function updateSettings($data, $logo_name = null) {
        $db = $this->getConnection();
        
        $shop_name = $db->real_escape_string($data['shop_name']);
        $company_name = $db->real_escape_string($data['company_name']);
        $business_license = $db->real_escape_string($data['business_license']);
        $phone = $db->real_escape_string($data['phone']);
        $email = $db->real_escape_string($data['email']);
        $address = $db->real_escape_string($data['address']);
        
        $wk1 = $db->real_escape_string($data['working_hours_1']);
        $wk2 = $db->real_escape_string($data['working_hours_2']);
        
        $p1 = $db->real_escape_string($data['policy_1']);
        $p2 = $db->real_escape_string($data['policy_2']);
        $p3 = $db->real_escape_string($data['policy_3']);
        $p4 = $db->real_escape_string($data['policy_4']);
        $p5 = $db->real_escape_string($data['policy_5']);

        $fb = $db->real_escape_string($data['facebook_url']);
        $ig = $db->real_escape_string($data['instagram_url']);
        $yt = $db->real_escape_string($data['youtube_url']);
        $tt = $db->real_escape_string($data['tiktok_url']);
        $zl = $db->real_escape_string($data['zalo_url']);

        $sql = "UPDATE shop_settings SET 
                shop_name = '$shop_name', company_name = '$company_name', business_license = '$business_license',
                phone = '$phone', email = '$email', address = '$address',
                working_hours_1 = '$wk1', working_hours_2 = '$wk2',
                policy_1 = '$p1', policy_2 = '$p2', policy_3 = '$p3', policy_4 = '$p4', policy_5 = '$p5',
                facebook_url = '$fb', instagram_url = '$ig', youtube_url = '$yt', tiktok_url = '$tt', zalo_url = '$zl'";
        
        if ($logo_name) {
            $sql .= ", logo_url = '$logo_name'";
        }
        
        $sql .= " WHERE id = 1";
        
        return $db->query($sql);
    }
}
<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Illuminate\Pagination\Paginator as Paginate;
//ggmou
class Product extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     * function getProductIdByCode() //get product id by product code
     * function getProductImageByProductId() //get product image by product id
     * function getProductPriceByProductId() //get product pricing by product id and price type id
     */

    public function getMembershipRate($data='')
    {
        $query = "SELECT b.* FROM prd_master as a INNER JOIN prd_price b ON a.id=b.prd_master_id";
        $query .= " WHERE a.status='A' AND b.status='A' AND a.name like '%reg%' AND b.start_date<=CURDATE() AND (b.end_date>=CURDATE() OR b.end_date IS NULL)";

        if(!empty($data['price_type_country'])){
            $price_type_list = implode(",", $data['price_type_country']);
            $query .= " AND b.prd_price_type_id in(".$price_type_list.")";
        }

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getPriceTypeCountryConfig($country_id)
    {
        $query = "SELECT a.*,b.code,b.name FROM prd_price_country a INNER JOIN prd_price_type b ON a.prd_price_type_id=b.id 
        WHERE a.status='A' AND a.country_id=" . $country_id . " ORDER BY a.seq_no";
        $result = DB::select($query);

        $result = json_decode(json_encode($result), true);
        
        return $result;
    }

    public function getPriceTypeCountryConfigObj($country_id)
    {
        $query = "SELECT a.*,b.code,b.name FROM prd_price_country a INNER JOIN prd_price_type b ON a.prd_price_type_id=b.id 
        WHERE a.status='A' AND a.country_id=" . $country_id . " ORDER BY a.seq_no";
        $result = DB::select($query);

        return $result;
    }

    public function getPriceTypeByCountry($country_id)
    {
        $query = "SELECT a.*,b.code,b.name FROM prd_price_country a INNER JOIN prd_price_type b ON a.prd_price_type_id=b.id 
        WHERE a.status='A' AND a.country_id=" . $country_id . " ORDER BY a.seq_no";
        $result = DB::select($query);

        $result = json_decode(json_encode($result), true);
        return $result;
    }

    public function getPriceTypeByMemberType($mem_type)
    {
        $query = "SELECT a.* FROM prd_price_mem_type a INNER JOIN prd_price_type b ON a.prd_price_type_id=b.id 
        WHERE a.status='A' AND a.mem_type='" . $mem_type . "' ORDER BY a.seq_no";
        $result = DB::select($query);

        $result = json_decode(json_encode($result), true);
        return $result;
    }

    public function getPriceTypeByMemberTypeAndCountryConfigObj($mem_type, $country_id)
    {
        $query = "SELECT a.*,b.code,b.name FROM prd_price_mem_type a 
        INNER JOIN prd_price_type b ON a.prd_price_type_id=b.id
        INNER JOIN prd_price_country c ON c.prd_price_type_id = a.prd_price_type_id
        WHERE a.status='A' AND a.mem_type='" . $mem_type . "' AND c.status = 'A' AND c.country_id = ". $country_id ." ORDER BY a.seq_no";
        $result = DB::select($query);

        return $result;
    }

    public function getProductIdByCode($code)
    {
        $query = 'SELECT id FROM prd_master WHERE code ="' . $code . '" AND status="A"';
        $result = DB::select($query);
        $result = json_decode(json_encode($result), true);

        return $result[0]['id'];
    }

    public function getUOMById($id)
    {
        $query = 'SELECT a.id as id,a.code,a.name,a.seq_no,a.status 
                  FROM sys_general a INNER JOIN sys_territory b ON a.country_id=b.id 
                  WHERE a.id =' . $id;

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getProductDescByLang($id, $lang)
    {
        $query = 'SELECT * FROM prd_master_desc a WHERE prd_master_id=' . $id . ' AND language_id="' . $lang . '"';
        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getProductBrandsList($isTop = '')
    {
        $query = 'SELECT a.id as id,a.code,a.name,replace(LOWER(a.name)," ","-") as prd_url,a.status,a.avatar,a.path,CONCAT(SUBSTRING_INDEX(a.path, "public/", -1),"/",a.avatar) as img_path, CONCAT(SUBSTRING_INDEX(a.path, "public/", -1),"/",a.avatar) as img_path FROM prd_brand a WHERE a.status ="A"';

        if ($isTop != '') {
            $query .= ' AND top=' . $isTop;
        }

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function getProductBrandsListByCompanyId($id)
    {
        $query = 'SELECT a.id as id,a.code,a.name,replace(LOWER(a.name)," ","-") as prd_url,a.status,a.avatar,a.path,CONCAT(SUBSTRING_INDEX(a.path, "public/", -1),"/",a.avatar) as img_path, CONCAT(SUBSTRING_INDEX(a.path, "public/", -1),"/",a.avatar) as img_path FROM prd_brand a WHERE a.status ="A" AND company_id = '.$id.' ';

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function getPriceTypeById($id)
    {
        $query = 'SELECT *
                  FROM prd_price_type
                  WHERE id =' . $id;

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getCategoryTreeList($countryId, $companyId)
    {
        $query = 'SELECT *
                  FROM prd_category
                  WHERE status = "A" AND
                  country_id='.$countryId.' AND
                  company_id='.$companyId.'
                  ORDER BY seq_no,parent_id';

        //remark check company id and only check country id
//        $query = 'SELECT *
//                  FROM prd_category
//                  WHERE status = "A" AND
//                  country_id=' . $countryId . '
//                  ORDER BY seq_no,parent_id';

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getCategoryTreeListByParent($countryId, $companyId, $code)
    {
        //remark check company id and only check country id
//        $query = 'SELECT *
//                  FROM prd_category a
//                  INNER JOIN prd_category b ON a.id = b.parent_id
//                  WHERE a.status = "A" AND b.status="A" AND a.code = "'.$code.'" ORDER BY a.seq_no,a.parent_id';

        $query = 'SELECT * 
                  FROM prd_category a 
                  INNER JOIN prd_category b ON a.id = b.parent_id 
                  WHERE a.status = "A" AND b.status = "A" AND a.code = "'.$code.'" AND a.company_id = '.$companyId.' ORDER BY a.seq_no, a.parent_id';

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getCategoryByCompanyId($companyId)
    {
        $query = "SELECT a.id as product_category_id, a.code, a.name, b.avatar, b.label, replace(b.path,'public/','') as path, b.date_upload
        FROM prd_category a 
        LEFT JOIN prd_category_image b ON a.id = b.prd_category_id 
        WHERE a.status = 'A' AND 
        a.parent_id = 0 AND 
        a.company_id=" . $companyId;

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getCategoryById($Id)
    {
        $query = "SELECT a.country_id, a.company_id, a.id as product_category_id, a.code,  a.name, replace(LOWER(a.name),' ','-') as prd_url, a.avatar, a.label, replace(a.path,'public/','') as path, a.parent_id,a.tax_category_id,CONCAT(SUBSTRING_INDEX(a.path, 'public/', -1),'/',a.avatar) as img_path,a.status,a.seq_no
        FROM prd_category a 
        WHERE a.id=" . $Id;
        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getCategoryIdByCode($code)
    {
        $query = "SELECT a.country_id, a.company_id, a.id as product_category_id, a.code, a.name,replace(LOWER(a.name),' ','-') as prd_url, b.avatar, b.label, b.path, CONCAT(SUBSTRING_INDEX(b.path, 'public/', -1),'/',b.avatar) as img_path, b.date_upload, a.parent_id
        FROM prd_category a 
        LEFT JOIN prd_category_image b ON a.id = b.prd_category_id AND b.status='A'
        WHERE a.status = 'A' AND 
        a.code='" . $code . "'";

        $result = DB::select($query);

        return json_decode(json_encode($result[0]), true);
    }

    public function getBrandIdByCode($code)
    {
        $ori_name = str_replace('-',' ',$code);
        /*$query = "SELECT a.country_id, a.status, a.company_id, a.id as product_brand_id, a.code, a.name, replace(LOWER(a.name),' ','-') as prd_url, a.avatar, replace(a.path,'public/','') as path
        FROM prd_brand a 
        WHERE a.status = 'A' AND 
        (a.code='" . $code . "' or a.name='".$ori_name."')";*/
        $query = 'SELECT a.country_id, a.status, a.company_id, a.id as product_brand_id, a.code, a.name, replace(LOWER(a.name)," ","-") as prd_url, a.avatar, replace(a.path,"public/","") as path
        FROM prd_brand a 
        WHERE a.status = "A" AND 
        (a.code="' . $code . '" or a.name="'.$ori_name.'")';

        $result = DB::select($query);
        return json_decode(json_encode($result[0]), true);
    }

    public function getBrandById($Id)
    {
        $query = "SELECT a.country_id, a.status, a.company_id, a.id, a.code, a.name,replace(LOWER(a.name),' ','-') as prd_url, a.avatar, replace(a.path,'public/','') as path, CONCAT(SUBSTRING_INDEX(a.path, 'public/', -1),'/',a.avatar) as img_path
        FROM prd_brand a 
        WHERE a.id=" . $Id;

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getAdminProductDetailById($id)
    {
        $product = $this->getProductMasterById($id);

        $result['product'] = $product;

        $desc_sql = "SELECT *
        FROM prd_master_desc a
        WHERE a.prd_master_id=" . $id;
        $result['desc'] = DB::select($desc_sql);

        $price_sql = "SELECT a.*,b.code, b.name,c.currency_code
        FROM prd_price a
        LEFT JOIN prd_price_type b on a.prd_price_type_id=b.id
        LEFT JOIN ent_country c ON b.country_id = c.id
        WHERE a.prd_master_id=" . $id;
        $result['price'] = DB::select($price_sql);

        if (sizeof($product[0]['prd_category_id']) > 0) {
            $category = implode(',', json_decode($product[0]['prd_category_id']));
            $category_sql = "SELECT a.id, a.code, a.name
            FROM prd_category a 
            WHERE a.id IN (" . $category . ") AND 
            a.status='A'";

            $result['category'] = DB::select($category_sql);
        }

        $img_sql = "SELECT a.*,CONCAT(SUBSTRING_INDEX(a.path, 'public/', -1),'/',a.avatar) as img_path,b.name as status_desc
        FROM prd_master_image a 
        INNER JOIN sys_general b ON a.status = b.code AND b.type = 'general-status'
        WHERE a.prd_master_id=" . $id . " AND a.status='A' ORDER BY a.default_img desc, a.status, a.id";

        $result['img'] = DB::select($img_sql);

        $product_brands = json_decode($product[0]['prd_brand_id']);
        if ($product_brands != 0) {
            $brands = implode(',', json_decode($product[0]['prd_brand_id']));
            $brand_sql = "SELECT a.id, a.code, a.name,a.avatar,a.path,CONCAT(SUBSTRING_INDEX(a.path, 'public/', -1),'/',a.avatar) as img_path FROM prd_brand a WHERE a.id IN (" . $brands . ") AND a.status='A'";
            $result['brand'] = DB::select($brand_sql);
        }

        return json_decode(json_encode($result), true);
    }
    
    public function getPriceCalcFormula()
    {
        $query = 'SELECT * FROM prd_price_calc';
        $result = DB::select($query);

        return json_decode(json_encode($result[0]), true);
    }

    public function getMemberRegPackage($data, $gateway = '')
    {
        $query = 'SELECT a.* FROM prd_master as a INNER JOIN prd_price b ON a.id=b.prd_master_id';
        $query .= ' WHERE a.admin=1 AND a.status="A" AND a.register=1 AND b.start_date<=CURDATE() AND (b.end_date>=CURDATE() OR b.end_date IS NULL)';

        if(!empty($data['price_type_country'])){
            $price_type_list = implode(",", array_column($data['price_type_country'], "prd_price_type_id"));
            $query .= ' AND b.prd_price_type_id in('.$price_type_list.')';
        }

        if(!empty($gateway)){
            if($gateway == 'admin'){
                $query .= ' OR a.backend=1 OR (a.id in (1,20) AND b.prd_price_type_id in('.$price_type_list.') )';
            }
        }

        $query .= ' GROUP BY a.id';
        $result = DB::select($query);
        return json_decode(json_encode($result), true);
//        if (!empty($data['country_id'])) {
//            $query .= " AND a.country_id=" . $data['country_id'];
//        }
    }

    public function getMemberTopupPackage($data, $gateway='')
    {
        $query = 'SELECT a.* FROM prd_master as a INNER JOIN prd_price b ON a.id=b.prd_master_id';
        $query .= ' WHERE a.admin=1 AND a.topup=1 AND a.status="A" AND b.start_date<=CURDATE() AND (b.end_date>=CURDATE() OR b.end_date IS NULL)';

        if(!empty($data['price_type_country'])){
            $price_type_list = implode(",", array_column($data['price_type_country'], "prd_price_type_id"));
            $query .= ' AND b.prd_price_type_id in('.$price_type_list.')';
        }

        if(!empty($gateway)){
            if($gateway == 'admin'){
                $query .= ' OR a.backend=1 OR (a.id in (1,20) AND b.prd_price_type_id in('.$price_type_list.') )';
            }
        }

        $query .= ' GROUP BY a.id';
        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function getMemberRepurchasePackage($data)
    {
        $query = 'SELECT a.* FROM prd_master as a INNER JOIN prd_price b ON a.id=b.prd_master_id ';
        $query .= ' WHERE a.admin=1 AND a.repurchase=1 AND a.status="A" AND b.start_date<=CURDATE() AND (b.end_date>=CURDATE() OR b.end_date IS NULL)';

        if(!empty($data['price_type_country'])){
            $price_type_list = implode(",", array_column($data['price_type_country'], "prd_price_type_id"));
            $query .= ' AND b.prd_price_type_id in('.$price_type_list.')';
        }

        $query .= ' GROUP BY a.id';
        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function getMemberPrepaidPackage($data)
    {
        $query = 'SELECT a.* FROM prd_master as a INNER JOIN prd_price b ON a.id=b.prd_master_id';
        $query .= ' WHERE a.admin=1 AND a.prepaid=1 AND a.status="A" AND b.start_date<=CURDATE() AND (b.end_date>=CURDATE() OR b.end_date IS NULL)';

        if(!empty($data['price_type_country'])){
            $price_type_list = implode(",", array_column($data['price_type_country'], "prd_price_type_id"));
            $query .= ' AND b.prd_price_type_id in('.$price_type_list.')';
        }

        $query .= ' GROUP BY a.id';
        $result = DB::select($query);
        
        return json_decode(json_encode($result), true);
    }

    public function getMemberRegPackagePrice($data,$gateway = '')
    {
        $query = 'SELECT a.*,a.code as product_code,b.*,b.id as price_id,c.code as price_type_code,c.name as price_type_desc FROM prd_master as a INNER JOIN prd_price b ON b.prd_master_id=a.id INNER JOIN prd_price_type c ON b.prd_price_type_id=c.id
        WHERE a.status="A" AND b.start_date<=CURDATE() AND (b.end_date>=CURDATE() OR b.end_date IS NULL)';

//        if (!empty($data['country_id'])) {
//            $query .= " AND a.country_id=" . $data['country_id'];
//        }
        if (!empty($data['price_type_id']) && empty($data['prd_price_type_id'])) {
            $query .= " AND b.prd_price_type_id in (" . $data['price_type_id'] . ")";
        } else {
            $query .= " AND b.prd_price_type_id=" . $data['prd_price_type_id'];
        }
        if (!empty($data['product_id'])) {
            $query .= " AND a.id=" . $data['product_id'];
        }

        if (!empty($data['package_id'])) {
            $query .= " AND a.package_id=" . $data['package_id'];
        }

        if(!empty($gateway)){
            if($gateway == 'admin'){
                $query .= ' and (a.backend=1 or a.admin=1)';
            }else {
                $query .= ' and a.admin=1';
            }
        } else {
            $query .= ' and a.admin=1';
        }
        //file_put_contents("/var/www/html/vvc/payment_gateway.log",print_r($query,true));
        // if($_SERVER['REMOTE_ADDR'] == '121.122.83.70'){
        //     print_r($gateway);
        //     print_r($query);
        // }
        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getProductDetailById($id)
    {
        $product = $this->getProductMasterById($id);

        $result['product'] = $product;

        $desc_sql = "SELECT *
        FROM prd_master_desc a
        WHERE a.prd_master_id=" . $id;
        $result['desc'] = DB::select($desc_sql);

        // b.code, b.name, replace(LOWER(b.name),' ','-') as prd_url, c.currency_code
        $price_sql = "SELECT a.*, c.currency_code
        FROM prd_price a
        -- INNER JOIN prd_price_type b on a.prd_price_type_id=b.id
        INNER JOIN ent_country c ON a.country_id = c.id
        WHERE a.start_date<=CURDATE() AND (a.end_date>=CURDATE() OR a.end_date IS NULL) AND a.status = 'A' AND a.prd_master_id=" . $id;

        $result['price'] = DB::select($price_sql);

        if (sizeof($product[0]['prd_category_id']) > 0) {
            $category = implode(',', json_decode($product[0]['prd_category_id']));
            $category_sql = "SELECT a.id, a.code, a.name
            FROM prd_category a 
            WHERE a.id IN (" . $category . ") AND 
            a.status='A'";

            $result['category'] = DB::select($category_sql);
        }

        $img_sql = "SELECT a.*,CONCAT(SUBSTRING_INDEX(a.path, 'public/', -1),'/',a.avatar) as img_path,b.name as status_desc
        FROM prd_master_image a 
        INNER JOIN sys_general b ON a.status = b.code AND b.type = 'general-status'
        WHERE a.prd_master_id=" . $id . " AND a.status='A' ORDER BY a.default_img desc, a.status, a.id";

        $result['img'] = DB::select($img_sql);

        $product_brands = json_decode($product[0]['prd_brand_id']);
//        if(sizeof($product[0]['prd_brand_id'])>0 && $product[0]['prd_brand_id']!=0){
        if ($product_brands != 0) {
//            $brands = $product[0]['prd_brand_id'];
            $brands = implode(',', json_decode($product[0]['prd_brand_id']));
            $brand_sql = "SELECT a.id, a.code, a.name, replace(LOWER(a.name),' ','-') as prd_url,a.avatar,a.path,CONCAT(SUBSTRING_INDEX(a.path, 'public/', -1),'/',a.avatar) as img_path FROM prd_brand a WHERE a.id IN (" . $brands . ") AND a.status='A'";
            $result['brand'] = DB::select($brand_sql);
        }

        return json_decode(json_encode($result), true);
    }

    public function getNewArrivalProducts($countryId, $companyId)
    {
        $query = "SELECT a.id,a.code,a.name,replace(LOWER(a.name),' ','-') as prd_url,a.gt as current_unit_sv,a.discount,b.avatar,b.label,b.path, CONCAT(SUBSTRING_INDEX(b.path, 'public/', -1),'/',b.avatar) as img_path, c.unit_price as current_unit_price,c.unit_bv as current_unit_bv,c.prd_price_type_id as current_prd_price_type_id ,c.id as current_prd_price_id,f.percent as current_gst_percent,a.new,a.best_seller,a.online,a.promotion,e.currency_code, d.name as product_name, d.language_id as lang
        FROM prd_master a
        INNER JOIN prd_master_image b ON a.id = b.prd_master_id and b.default_img=1 and b.status='A'
        INNER JOIN prd_price c ON a.id = c.prd_master_id and c.status='A'
        INNER JOIN prd_master_desc d ON a.id = d.prd_master_id
        INNER JOIN ent_country e ON a.country_id = e.id
        INNER JOIN sys_tax_type f ON a.tax_type_id=f.id
        WHERE a.new = 1 AND a.status='A' AND c.start_date<=CURDATE() AND (c.end_date>=CURDATE() OR c.end_date IS NULL) AND a.country_id=" . $countryId;
        $result = DB::select($query);
        // dd($query);
        return json_decode(json_encode($result), true);
    }

    public function getBestSellerProducts($countryId, $companyId, $data = '')
    {
        if (!empty($data['itemperpage'])) {
            $record = $data['itemperpage'];
        } else {
            $record = 20;
        }
        $query = DB::table('prd_master as a');
        $query = $query->leftJoin('prd_master_image as b', 'a.id', '=', 'b.prd_master_id');
        $query = $query->Join('prd_price as c', 'a.id', '=', 'c.prd_master_id');
        $query = $query->Join('prd_master_desc as d', 'a.id', '=', 'd.prd_master_id');
        $query = $query->Join('ent_country as e', 'c.country_id', '=', 'e.id');
        $query = $query->Join('sys_tax_type as f', 'a.tax_type_id', '=', 'f.id');
        $query = $query->where('b.default_img', 1);
        $query = $query->where('a.status', 'A');
        $query = $query->where('b.status', 'A');
        $query = $query->where('c.status', 'A');
        // $query = $query->where('c.prd_price_type_id', 1);
        $query = $query->where('a.best_seller', 1);
        $query = $query->where('a.country_id', $countryId);
//        $query = $query->where('a.company_id',$companyId);
        $query = $query->select(DB::raw('a.id,a.code,a.name,replace(LOWER(a.name)," ","-") as prd_url,a.gt as current_unit_sv,a.discount,b.avatar,b.label,b.path, CONCAT(SUBSTRING_INDEX(b.path, "public/", -1),"/",b.avatar) as img_path,a.new,a.best_seller,a.online,a.promotion,c.unit_price as current_unit_price,c.unit_bv as current_unit_bv,c.prd_price_type_id as current_prd_price_type_id ,c.id as current_prd_price_id,f.percent as current_gst_percent,e.currency_code,d.language_id as lang'));
        $objData = $query->paginate($record);

        return $objData;
//        return json_decode(json_encode($result), true); 
    }

    public function checkPromotionByProductId($product_id, $price_type_id)
    {
        $sql = 'SELECT b.id as price_id FROM prd_master as a INNER JOIN prd_price b ON b.prd_master_id=a.id AND b.prd_price_type_id=' . $price_type_id . ' WHERE a.promotion=1 AND a.id=' . $product_id . ' AND b.start_date<=CURDATE() AND (b.end_date>=CURDATE() OR b.end_date IS NULL)';
        $result = DB::select($sql);

        return json_decode(json_encode($result), true);
    }

    public function getPromotionProducts($countryId, $companyId, $data = '')
    {
        $sql = 'SELECT b.id as price_id FROM prd_master as a INNER JOIN prd_price b ON b.prd_master_id=a.id WHERE  a.promotion=1 AND b.start_date<=CURDATE() AND (b.end_date>=CURDATE() OR b.end_date IS NULL)';
        $result = DB::select($sql);

        if (!empty($data['itemperpage'])) {
            $record = $data['itemperpage'];
        } else {
            $record = 10;
        }
        $query = DB::table('prd_master as a');
        $query = $query->leftJoin('prd_master_image as b', 'a.id', '=', 'b.prd_master_id');
        $query = $query->Join('prd_price as c', 'a.id', '=', 'c.prd_master_id');
        // $query = $query->Join('prd_price_type as d', 'c.prd_price_type_id', '=', 'd.id');
        $query = $query->Join('prd_master_desc as d', 'd.prd_master_id', '=', 'a.id');
        $query = $query->Join('ent_country as e', 'c.country_id', '=', 'e.id');
        $query = $query->Join('sys_tax_type as f', 'a.tax_type_id', '=', 'f.id');
        $query = $query->where('a.status', 'A');
        $query = $query->where('b.status', 'A');
        $query = $query->where('c.status', 'A');
        $query = $query->where('b.default_img', 1);
        // $query = $query->where('c.prd_price_type_id', $price_id);
        $query = $query->where('a.promotion', 1);
        $query = $query->where('a.country_id', $countryId);
        // $query = $query->where('a.company_id', $companyId);
        $query = $query->whereRaw('c.start_date<=CURDATE() AND (c.end_date>=CURDATE() OR c.end_date IS NULL)');
        $query = $query->select(DB::raw('a.id,a.code,a.name,replace(LOWER(a.name)," ","-") as prd_url,a.gt as current_unit_sv,a.discount,b.avatar,b.label,b.path, CONCAT(SUBSTRING_INDEX(b.path, "public/", -1),"/",b.avatar) as img_path,a.new,a.best_seller,a.online,a.promotion,c.unit_price as current_unit_price,c.unit_bv as current_unit_bv,c.prd_price_type_id as current_prd_price_type_id ,c.id as current_prd_price_id,f.percent as current_gst_percent,e.currency_code, d.name as product_name, d.language_id as lang'));
        $query = $query->orderBy('a.seq_no', 'ASC');
        $objData = $query->paginate($record);
        return $objData;
//        return json_decode(json_encode($result), true);
    }

    public function getProductDetailByCategory($id, $data, $searchParam = [])
    {

        if (!empty($data['itemperpage'])) {
            $record = $data['itemperpage'];
        } else {
            $record = 15;
        }

        if (!empty($data['sortby'])) {
            if ($data['sortby'] == 'price_asc') {
                $sort = 'd.unit_price';
                $sort_direction = 'asc';
            } elseif ($data['sortby'] == 'price_desc') {
                $sort = 'd.unit_price';
                $sort_direction = 'desc';
            } elseif ($data['sortby'] == 'name_asc') {
                $sort = 'a.name';
                $sort_direction = 'asc';
            } elseif ($data['sortby'] == 'name_desc') {
                $sort = 'a.name';
                $sort_direction = 'desc';
            }
        } else {
            $sort = 'a.code';
            $sort_direction = 'asc';
        }

        $query = DB::table('prd_master as a');
        $query = $query->leftJoin('prd_master_image as b', 'a.id', '=', 'b.prd_master_id');
        $query = $query->Join('prd_master_desc as c', 'a.id', '=', 'c.prd_master_id');
        $query = $query->leftJoin('prd_price as d', 'a.id', '=', 'd.prd_master_id');
        $query = $query->leftJoin('prd_price_type as e', 'd.prd_price_type_id', '=', 'e.id');
        $query = $query->leftJoin('ent_country as f', 'e.country_id', '=', 'f.id');
        $query = $query->where('a.status', 'A');
        $query = $query->where('b.default_img', '1');
        $query = $query->where('c.language_id', 'en');
        $query = $query->where('d.prd_price_type_id', 2);
        if (sizeof($searchParam) > 0) {
            foreach ($searchParam as $row => $search) {
                if ($search[0] == 'price_to') {
                    $query = $query->where('d.unit_price', $search[1], $search[2]);
                } elseif ($search[0] == 'price_from') {
                    $query = $query->where('d.unit_price', $search[1], $search[2]);
                }
            }
        }
        $query = $query->whereRaw('d.start_date<=CURDATE() AND (d.end_date>=CURDATE() OR d.end_date IS NULL)');
        $query = $query->whereRaw('JSON_CONTAINS(a.prd_category_id,"' . $id . '")');
        $query = $query->select(DB::raw('a.id,a.code,a.gt,a.name,replace(LOWER(a.name)," " ,"-") as prd_url,b.avatar,b.label,b.path, CONCAT(SUBSTRING_INDEX(b.path, "public/", -1),"/",b.avatar) as img_path,a.new,a.best_seller,a.online,a.promotion,a.display,c.short_desc,d.id as current_prd_price_id,d.unit_price as current_unit_price,d.unit_bv as current_unit_bv,e.code as current_price_code,f.currency_code'));
        $query = $query->orderBy($sort, $sort_direction);
        $objData = $query->paginate($record);

        return $objData;
    }

    public function getProductDetailByBrand($id, $data, $searchParam = [], $order = '')
    {

        if (!empty($data['itemperpage'])) {
            $record = $data['itemperpage'];
        } else {
            $record = 15;
        }

//        if (!empty($data['sortby'])) {
//            if ($data['sortby'] == 'price_asc') {
//                $sort = 'd.unit_price';
//                $sort_direction = 'asc';
//            } elseif ($data['sortby'] == 'price_desc') {
//                $sort = 'd.unit_price';
//                $sort_direction = 'desc';
//            } elseif ($data['sortby'] == 'name_asc') {
//                $sort = 'a.name';
//                $sort_direction = 'asc';
//            } elseif ($data['sortby'] == 'name_desc') {
//                $sort = 'a.name';
//                $sort_direction = 'desc';
//            }
//        } else {
//            $sort = 'a.code';
//            $sort_direction = 'asc';
//        }
//
//        $query = DB::table('prd_master as a');
//        $query = $query->leftJoin('prd_master_image as b', 'a.id', '=', 'b.prd_master_id');
//        $query = $query->Join('prd_master_desc as c', 'a.id', '=', 'c.prd_master_id');
//        $query = $query->Join('prd_price as d', 'a.id', '=', 'd.prd_master_id');
//        $query = $query->Join('prd_price_type as e', 'd.prd_price_type_id', '=', 'e.id');
//        $query = $query->Join('ent_country as f', 'e.country_id', '=', 'f.id');
//        $query = $query->where('a.status', 'A');
//        $query = $query->where('d.status', 'A');
//        $query = $query->where('b.default_img', '1');
//        $query = $query->where('c.language_id', 'en');
//        $query = $query->where('d.prd_price_type_id', 2);
//        if (sizeof($searchParam) > 0) {
//            foreach ($searchParam as $row => $search) {
//                if ($search[0] == 'price_to') {
//                    $query = $query->where('d.unit_price', $search[1], $search[2]);
//                } elseif ($search[0] == 'price_from') {
//                    $query = $query->where('d.unit_price', $search[1], $search[2]);
//                }
//            }
//        }
//        $query = $query->whereRaw('d.start_date<=CURDATE() AND (d.end_date>=CURDATE() OR d.end_date IS NULL)');
//        $query = $query->whereRaw('JSON_CONTAINS(a.prd_brand_id,"' . $id . '")');
//        $query = $query->select(DB::raw('a.id,a.code,a.gt,a.name,replace(LOWER(a.name)," " ,"-") as prd_url,b.avatar,b.label,b.path, CONCAT(SUBSTRING_INDEX(b.path, "public/", -1),"/",b.avatar) as img_path,a.new,a.best_seller,a.online,a.promotion,a.display,c.short_desc,d.id as current_prd_price_id,d.unit_price as current_unit_price,d.unit_bv as current_unit_bv,e.code as current_price_code,f.currency_code'));
//        $query = $query->orderBy($sort, $sort_direction);
//
//        $objData = $query->paginate($record);

        $query = DB::table('prd_master as a');
        $query = $query->leftJoin('prd_master_image as b', 'a.id', '=', 'b.prd_master_id');
        $query = $query->Join('prd_master_desc as c', 'a.id', '=', 'c.prd_master_id');
        $query = $query->Join('prd_price as d', 'a.id', '=', 'd.prd_master_id');
        // $query = $query->Join('prd_price_type as e', 'd.prd_price_type_id', '=', 'e.id');
        $query = $query->Join('ent_country as f', 'd.country_id', '=', 'f.country_id');
//        $query = $query->leftJoin('prd_margin as h', 'a.id', '=', 'h.prd_master_id');
        $query = $query->where('a.status', 'A');
        $query = $query->where('d.status', 'A');
        $query = $query->where('b.default_img', '1');
        $query = $query->where('c.language_id', 'en');
        // $query = $query->where('d.prd_price_type_id', '1');
//        $query = $query->where('h.country_id', 1);
        if (sizeof($searchParam) > 0) {
            foreach ($searchParam as $row => $search) {
                if ($search[0] == 'price_to') {
                    $query = $query->where('d.unit_price', $search[1], $search[2]);
                } elseif ($search[0] == 'price_from') {
                    $query = $query->where('d.unit_price', $search[1], $search[2]);
                }
            }
        }
        $query = $query->whereRaw('d.start_date<=CURDATE() AND (d.end_date>=CURDATE() OR d.end_date IS NULL)');
        $query = $query->whereRaw('a.prd_brand_id != "" ');
        $query = $query->whereRaw('JSON_CONTAINS(a.prd_brand_id,"' . $id . '")');
        if($order == 'price-desc'){
            $query = $query->orderBy('d.unit_price', 'desc');
        }elseif($order == 'price-asc'){
            $query = $query->orderBy('d.unit_price', 'asc');
        }elseif($order == 'name-desc'){
            $query = $query->orderBy('a.name', 'desc');
        }elseif($order == 'name-asc'){
            $query = $query->orderBy('a.name', 'asc');
        }
        $query = $query->select(DB::raw('a.id,a.code,a.gt,a.name,replace(LOWER(a.name)," " ,"-") as prd_url,b.avatar,b.label,b.path, CONCAT(SUBSTRING_INDEX(b.path, "public/", -1),"/",b.avatar) as img_path,a.new,a.best_seller,a.online,a.promotion,a.display,c.short_desc,d.id as current_prd_price_id,d.unit_price as current_unit_price,d.unit_bv as current_unit_bv,f.currency_code'));
        $objData = $query->paginate($record, ['*'], 'page_na');
        // dd($objData);
        return $objData;
    }

    public static function getProductMasterById($id)
    {
        $query = 'SELECT a.*
        FROM prd_master a
        WHERE a.id=' . $id;

        $result = DB::select($query);
        $result = json_decode(json_encode($result), true);

        if(!empty($result)){
            if(!empty($result[0]['option_names'])){
                $result[0]['option_names'] = json_decode($result[0]['option_names']);
            }

            if(!empty($result[0]['option_choices'])){
                $result[0]['option_choices'] = json_decode($result[0]['option_choices']);
            }
        }

        return $result;
    }

    public function getProductPackageById($id)
    {
        $query = 'SELECT b.code, b.name FROM prd_master a INNER JOIN sys_general b ON a.package_id = b.id AND b.type="reward-package" AND a.status="A" AND b.status="A" WHERE a.id='.$id;
        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getProductImageByProductId($id)
    { // valid function 2017
        $query = "SELECT a.*,CONCAT(SUBSTRING_INDEX(a.path, 'public/', -1),'/',a.avatar) as img_path,b.name as status_desc FROM prd_master_image a INNER JOIN sys_general b ON a.status = b.code AND b.type='general-status' WHERE a.default_img=1 AND a.prd_master_id =" . $id;
        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getAllProductImagesByProductId($id)
    { // valid function 2017
        $query = "SELECT a.*,CONCAT(SUBSTRING_INDEX(a.path, 'public/', -1),'/',a.avatar) as img_path,b.name as status_desc FROM prd_master_image a INNER JOIN sys_general b ON a.status = b.code AND b.type='general-status' WHERE a.status = 'A' AND a.prd_master_id =" . $id;
        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getProductPriceByProductId($id, $price_type = '')
    {
        // d.code as price_code
        $query = 'SELECT a.*,a.unit_sv as unit_gtoken,replace(LOWER(b.name)," " ,"-") as prd_url,b.gt as unit_sv,b.discount as unit_disc,b.code,b.name,b.option_names, b.option_choices,ifnull(if(b.id in (1,20),0.00,c.percent),0) as gst_percent, e.currency_code
        FROM prd_price a 
        INNER JOIN prd_master b ON a.prd_master_id=b.id AND b.status="A"
        LEFT JOIN sys_tax_type c ON b.tax_type_id=c.id
        -- INNER JOIN prd_price_type d ON a.prd_price_type_id=d.id 
        INNER JOIN ent_country e ON a.country_id=e.id
        WHERE a.prd_master_id=' . $id . ' AND a.start_date<=CURDATE() AND (a.end_date>=CURDATE() OR a.end_date IS NULL)';

        if (!empty($price_type)) {
            $query .= 'AND a.prd_price_type_id=' . $price_type;
        }

        $result = DB::select($query);

        if (!empty($result)) {
            $result = json_decode(json_encode($result), true);
            if(!empty($result)){
                if(!empty($result[0]['option_names'])){
                    $result[0]['option_names'] = json_decode($result[0]['option_names']);
                }

                if(!empty($result[0]['option_choices'])){
                    $result[0]['option_choices'] = json_decode($result[0]['option_choices']);
                }
            }

            return $result;
        } else {
            return [];
        }
    }

    public function getProductMasterDetail($id)
    {
        $query = 'select * from prd_master where id =' . $id;
        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getProductDetailBySearchKey($data, $searchParam = [], $type)
    {
        if (!empty($data['itemperpage'])) {
            $record = $data['itemperpage'];
        } else {
            $record = 15;
        }

        if (!empty($data['sortby'])) {
            if ($data['sortby'] == 'price_asc') {
                $sort = 'd.unit_price';
                $sort_direction = 'asc';
            } elseif ($data['sortby'] == 'price_desc') {
                $sort = 'd.unit_price';
                $sort_direction = 'desc';
            } elseif ($data['sortby'] == 'name_asc') {
                $sort = 'a.name';
                $sort_direction = 'asc';
            } elseif ($data['sortby'] == 'name_desc') {
                $sort = 'a.name';
                $sort_direction = 'desc';
            }
        } else {
            $sort = 'a.code';
            $sort_direction = 'asc';
        }

        $query = DB::table('prd_master as a');
        $query = $query->leftJoin('prd_master_image as b', 'a.id', '=', 'b.prd_master_id');
        $query = $query->Join('prd_master_desc as c', 'a.id', '=', 'c.prd_master_id');
        $query = $query->leftJoin('prd_price as d', 'a.id', '=', 'd.prd_master_id');
        $query = $query->leftJoin('prd_price_type as e', 'd.prd_price_type_id', '=', 'e.id');
        $query = $query->leftJoin('ent_country as f', 'e.country_id', '=', 'f.id');
        if($type == 'store') {
            //$query = $query->join('prd_brand as g', 'LEFT(RIGHT(a.prd_brand_id, LENGTH(a.prd_brand_id)-1),LENGTH(a.prd_brand_id)-2)' , '=' , 'g.id');
            $query = $query->join('prd_brand as g' , function ($q){
                $q->on('g.id', '=',
                    DB::raw('LEFT(RIGHT(prd_brand_id, LENGTH(prd_brand_id)-1),LENGTH(prd_brand_id)-2)'));

            });
        }
        $query = $query->where('b.default_img', 1);
        $query = $query->where('c.language_id', 'en');
        $query = $query->where('d.prd_price_type_id', 2);
        if($type == 'store') {
            if (sizeof($searchParam) > 0) {
                $query->where(function ($query) use ($searchParam) {
                    $query = $query->where('g.code', 'like', '%' . $searchParam['query'] . '%');
                    $query = $query->orwhere('g.name', 'like', '%' . $searchParam['query'] . '%');
                });
            }
        } else {
            if (sizeof($searchParam) > 0) {
                $query->where(function ($query) use ($searchParam) {
                    $query = $query->where('a.code', 'like', '%' . $searchParam['query'] . '%');
                    $query = $query->orWhere('a.name', 'like', '%' . $searchParam['query'] . '%');
                });
            }
        }

        $query = $query->where('a.status', 'A');
        $query = $query->where('d.status', 'A');
        $query = $query->where('b.status', 'A');
        $query = $query->whereRaw('d.start_date<=CURDATE() AND (d.end_date>=CURDATE() OR d.end_date IS NULL)');
        $query = $query->select(DB::raw('a.id,a.code,a.gt,a.name,replace(LOWER(a.name)," " ,"-") as prd_url,b.avatar,b.label,b.path, CONCAT(SUBSTRING_INDEX(b.path, "public/", -1),"/",b.avatar) as img_path,a.new,a.best_seller,a.online,a.promotion,a.display,c.short_desc,d.id as current_prd_price_id,d.unit_price as current_unit_price,d.unit_bv as current_unit_bv,e.code as current_price_code,f.currency_code'));
        $query = $query->orderBy($sort, $sort_direction);
        $objData = $query->paginate($record);
        //print_r($query->toSql());die();
        return $objData;
    }

    public function getMerchantProductListByMerchantId($id)
    {
        $query = "SELECT b.*,c.name as status_desc 
        FROM ent_company a 
        INNER JOIN prd_master b ON a.id=b.company_id
        INNER JOIN sys_general c ON b.status=c.code AND c.type='general-status'
        WHERE a.id=" . $id . " AND b.status != 'P' AND b.status != 'D'  ORDER BY b.id DESC";
        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }


    public function getLangList()
    {
        $result = Base::select('sys_language');

        return $result;
    }

    public function getTaxCategoryDetail($id)
    {
        $query = 'select * from prd_tax_category where id =' . $id;
        $array = DB::select($query);

        return $array;
    }


    public function getAvatarFromId($id)
    {
        $query = 'select avatar from prd_image where id =' . $id;
        $avatar = DB::select($query);

        return $avatar[0]->avatar;
    }

    public function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }

    public function buildStringTree(array $elements, $id)
    {
        $branch = null;
        //dd($elements);
        $catArray = explode(" ", $id);
        foreach ($elements as $element) {
            if ($element->level == 1) {
                if (in_array($element->id, $catArray)) {
                    $branch .= "<ul style='list-style: none;' class='list-unstyled'><li><label><input type='checkbox' id='category' name='category[]' value='" . $element->id . "' checked>" . $element->name . "</label>";
                } else {
                    $branch .= "<ul style='list-style: none;' class='list-unstyled'><li><label><input type='checkbox' id='category' name='category[]' value='" . $element->id . "'>" . $element->name . "</label>";
                }

                if (isset($element->children) && count($element->children) > 0) {
                    $branch .= "<ul style='list-style: none;' class='list-unstyled'>";
                    $children = $this->buildStringTree($element->children, $id);
                    $branch .= $children;
                    $branch .= "</ul>";
                }
            } else {
                if (in_array($element->id, $catArray)) {
                    $branch .= "<li style='list-style: none;'><label><input type='checkbox' id='category' name='category[]' value='" . $element->id . "' checked>" . $element->name . "</label>";
                } else {
                    $branch .= "<li style='list-style: none;'><label><input type='checkbox' id='category' name='category[]' value='" . $element->id . "'>" . $element->name . "</label>";
                }

                if (isset($element->children) && count($element->children) > 0) {
                    $branch .= "<ul style='list-style: none;' class='list-unstyled'>";
                    $children = $this->buildStringTree($element->children, $id);
                    $branch .= $children;
                    $branch .= "</ul>";
                }
            }
        }
        $branch .= "</ul>";

        return $branch;
    }

    public function getPendingApprovalProduct()
    {
        $query = "SELECT * FROM `prd_master` where status ='P' limit 4";
        $result = DB::select($query);
        return $result;
    }

    public function getPackageMemberTitleById($id)
    {
        $query = "SELECT a.id,a.code,a.name as package_name, a.package_id, b.name as mem_title,a.status 
        FROM prd_master a 
        LEFT JOIN sys_general b on b.id = a.package_id and b.type='reward-package' 
        where a.id = ".$id." AND a.admin = 1";

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function getProductPriceByProductIdForPrepaidCode($id, $price_type = '')
    {
        $query = 'SELECT a.*,a.unit_sv as unit_gtoken,b.gt as unit_sv,b.discount as unit_disc,b.code,b.name,ifnull(c.percent,0) as gst_percent,d.code as price_code,e.currency_code
        FROM prd_price a 
        INNER JOIN prd_master b ON a.prd_master_id=b.id AND b.status="A"
        LEFT JOIN sys_tax_type c ON b.tax_type_id=c.id
        INNER JOIN prd_price_type d ON a.prd_price_type_id=d.id 
        INNER JOIN ent_country e ON d.country_id=e.id
        WHERE a.prd_master_id=' . $id . ' AND a.start_date<=CURDATE() AND (a.end_date>=CURDATE() OR a.end_date IS NULL)';

        if (!empty($price_type)) {
            $query .= 'AND a.prd_price_type_id=' . $price_type;
        }
        $result = DB::select($query);

        if (!empty($result)) {
            return json_decode(json_encode($result), true);
        } else {
            return [];
        }
    }

    public function saveReview($data,$nick_name){
        if (!empty($data)) {
            $id = $data['prd_id'];
            $prd_name = $data['prd_name'];
            $name = $data['name'];
            $rating = $data['rating'];
            $review = $data['review'];
            $email = $data['email'];
            $member_id = $data['member_id'];
            // $result = $data;    
            $query = 
                'INSERT INTO prd_rating_review
                (prd_master_id, prd_name, member_id , name , rating, review, email, status,created_by,updated_by)
                VALUES ("' . $id . '","' . $prd_name . '","' . $member_id . '","' . $name . '",
                "' . $rating . '","' . $review . '","' . $email . '","p","' . $member_id . '","' . $member_id . '")
                ';
              
        }    
        $result = DB::select($query);
  
    }

    public function getProductReview($prd_id){
        $query = 
        'SELECT * 
        FROM prd_rating_review
        WHERE prd_master_id = "'.$prd_id.'" AND status = "A"
        ';

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function getProductRating($id){
        $query = 
        'SELECT a.id,a.email,a.rating,a.review,c.name as merchant_name,d.nick_name,a.created_at,e.name as status_desc , b.name as prd_name 
        FROM prd_rating_review a 
        INNER JOIN prd_master b on a.prd_master_id=b.id 
        inner join ent_company c on b.company_id=c.id 
        inner join ent_member d on a.member_id = d.id 
        inner join sys_general e on a.status = e.code and e.type="general-status"
        WHERE a.id = "'.$id.'"
        ';

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function approvalStatus($id,$status){
        // if (!empty($data)) {
            // $id = $data['id'];
            // $status_desc = $data['status_desc'];

            $query = '
            UPDATE prd_rating_review
            SET status = "'.$status.'"
            WHERE id = "'.$id.'"
            ';
        // }  

        $result = DB::select($query);
        return true;
    }

    public function getProductRatingListByMerchant($company_id) {
        $query ="select a.id,d.nick_name,a.email,a.rating,a.review,b.name as prd_name,c.name as merchant_name,a.status,a.created_at 
        from prd_rating_review a 
        inner join prd_master b on a.prd_master_id=b.id 
        inner join ent_company c on b.company_id=c.id 
        inner join ent_member d on a.member_id = d.id
        where c.id = ".$company_id." AND a.status = 'A'";

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function getRatingCount($id) {
        $query = '
        SELECT a.id,a.email,a.rating,a.review,c.name as merchant_name,d.nick_name,a.created_at,e.name as status_desc , b.name as prd_name 
        FROM prd_rating_review a 
        INNER JOIN prd_master b on a.prd_master_id=b.id 
        inner join ent_company c on b.company_id=c.id 
        inner join ent_member d on a.member_id = d.id 
        inner join sys_general e on a.status = e.code and e.type="general-status"
        WHERE a.prd_master_id = "'.$id.'" AND a.status = "A"
        ';

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function getProductRatingById($id){
        $query = '
        SELECT a.id,a.email,a.rating,a.review,c.name as merchant_name,d.nick_name,a.created_at,e.name as status_desc , b.name as prd_name 
        FROM prd_rating_review a 
        INNER JOIN prd_master b on a.prd_master_id=b.id 
        inner join ent_company c on b.company_id=c.id 
        inner join ent_member d on a.member_id = d.id 
        inner join sys_general e on a.status = e.code and e.type="general-status"
        WHERE a.prd_master_id = "'.$id.'" AND a.status = "A"
        ';

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }
    
    public function getRatingsAmountByPRDID($data)
    {
        $query = "SELECT COUNT(*) FROM prd_rating_review WHERE prd_master_id = '".$data."' AND status = 'AP'";
        $result = DB::select($query);
        
        return json_decode(json_encode($result), true);
    }
    
    public function getPricetypeCountry()
    {
        $query = "SELECT * FROM sys_territory WHERE territory_type = 'country' AND status = 'A'";
        $result = DB::select($query);
        
        return json_decode(json_encode($result), true);
    }
    
    public function getCurrencyCode()
    {
        $query = "SELECT currency_code FROM ent_country WHERE currency_code != ''";
        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }
    
    public function getSimpleSignupProductDetails($data)
    {
        $query = "SELECT GROUP_CONCAT(name SEPARATOR ' + ') as name from prd_master where id in (".$data.")";
        $result = DB::select($query);
        
        return json_decode(json_encode($result[0]), true);

    }
    
    public function getProductPackageNameByID($id)
    {
        $query = "SELECT name from prd_master where id=".$id;
        $result = DB::select($query);
        return json_decode(json_encode($result[0]), true);
    }

    public function getCategoryByCountryIdCompanyId($data = '') 
    {
        $query = "SELECT * FROM prd_category WHERE status = 'A' AND ((country_id = 1 AND company_id = 1)";

        if($data != ''){
            $query .= " OR (country_id = ".$data['country_id']." AND company_id = ".$data['company_id'].")";
        }

        $query .= ") ORDER BY seq_no,parent_id";

        $result = DB::select($query);
        return $result;
    }

    public function getBrandByCountryIdCompanyId($data) 
    {
        $query = "SELECT * FROM prd_brand WHERE status = 'A' AND country_id = ".$data['country_id']." AND company_id = ".$data['company_id'];

        $result = DB::select($query);
        return json_decode(json_encode($result), true);
    }

    public function checkPrdCartExists($data, $country_id)
    {
        $query = "SELECT * FROM prd_cart WHERE status = 'A' AND country_id ='".$country_id."' AND member_id = '".$data['member_id']."' AND prd_master_id = '".$data['prd_master_id']."'";

        if(!empty($data['prd_code'])){
            $query .= " AND code = '".$data['prd_code']."'";
        }

        if(!empty($data['prd_type'])){
            $query .= " AND prd_type = '".$data['prd_type']."'";
        }
//        else{
//            $query .= " AND prd_type is NULL";
//        }

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getPrdMarginDetailById($prd_id, $country_id)
    {
        $query = "SELECT * FROM prd_margin WHERE prd_master_id = '".$prd_id."' AND country_id = '".$country_id."'";

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getPrdCartDetails($country_id, $member_id)
    {
        $query = "SELECT a.*,b.qty_on_hand,b.name,replace(LOWER(b.name),' ','-') as prd_url,c.currency_code,d.id as prd_price_type_id FROM prd_cart a LEFT JOIN prd_master b ON a.prd_master_id = b.id LEFT JOIN ent_country c ON a.country_id = c.country_id LEFT JOIN prd_price_type d ON a.country_id = d.country_id WHERE a.status = 'A' AND a.country_id ='".$country_id."' AND a.member_id = '".$member_id."' AND d.vmore = 0";

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getTotalPrdCartAmount($country_id, $member_id)
    {
        $query = "SELECT COUNT(*) as total_prd_cart FROM prd_cart WHERE status = 'A' AND country_id = '".$country_id."' AND member_id = '".$member_id."'";

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getMemberPrdCartTotalAmount($data, $country_id)
    {
        $query = "SELECT qty,total_amount FROM prd_cart WHERE status = 'A' AND country_id ='".$country_id."' AND member_id = '".$data['member_id']."'";

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function checkPrdWishlistExists($data, $country_id)
    {
        $query = "SELECT * FROM prd_wishlist WHERE status = 'A' AND country_id ='".$country_id."' AND member_id = '".$data['member_id']."' AND prd_master_id = '".$data['prd_master_id']."'";

        if(!empty($data['prd_type'])){
            $query .= " AND prd_type = '".$data['prd_type']."'";
        }else{
            $query .= " AND prd_type is NULL";
        }

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getMemberPrdWishlistTotalAmount($data, $country_id)
    {
        $query = "SELECT qty,total_amount FROM prd_wishlist WHERE status = 'A' AND country_id ='".$country_id."' AND member_id = '".$data['member_id']."'";

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getPrdWishlistDetails($country_id, $member_id)
    {
        $query = "SELECT a.*,b.name,replace(LOWER(b.name),' ','-') as prd_url,c.currency_code,d.id as prd_price_type_id,b.qty_on_hand FROM prd_wishlist a LEFT JOIN prd_master b ON a.prd_master_id = b.id LEFT JOIN ent_country c ON a.country_id = c.country_id LEFT JOIN prd_price_type d ON a.country_id = d.country_id WHERE a.status = 'A' AND a.country_id ='".$country_id."' AND a.member_id = '".$member_id."' AND d.vmore = 0";

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getTotalPrdWishlistAmount($country_id, $member_id)
    {
        $query = "SELECT COUNT(*) as total_prd_wishlist FROM prd_wishlist WHERE status = 'A' AND country_id = '".$country_id."' AND member_id = '".$member_id."'";

        $result = DB::select($query);

        return json_decode(json_encode($result), true);
    }

    public function getDailyDealsProduct($country_id, $company_id){
        $query = "SELECT a.id, a.code, a.name, a.discount, b.avatar, b.label,  CONCAT(SUBSTRING_INDEX(b.path, 'public/', -1),'/',b.avatar) as img_path, a.daily, c.currency_code, e.unit_price as current_unit_price, e.prd_price_type_id as current_prd_price_type_id, e.id as current_prd_price_id, d.name as product_name, d.language_id as lang,replace(LOWER(a.name),' ','-') as prd_url, d.short_desc, e.start_date, e.end_date
        FROM prd_master a
        INNER JOIN prd_master_image b on a.id = b.prd_master_id and b.default_img=1 and b.status='A'
        INNER JOIN ent_country c ON a.country_id = c.id
        INNER JOIN prd_master_desc d ON a.id = d.prd_master_id
        INNER JOIN prd_price e ON a.id = e.prd_master_id and e.status='A'
        WHERE a.daily=1 and a.status='A' and a.country_id=" . $country_id . " and a.company_id=" . $company_id . " ";

        $result = DB::select($query);
        // dd($result);
        return json_decode(json_encode($result), true);
       
    }
}

package kr.ac.sejong.gotgan.Main

import org.json.JSONObject

class MasterHomeInOutData(jsonData : JSONObject) {
    var rentIndex : Int? = jsonData.get("rent_index").toString().toInt()

    var rentUserIndex : Int? = jsonData.get("rent_user_index").toString().toInt()
    var rentUserName : String? = jsonData.get("rent_user_name").toString()
    var rentUserId : String? = jsonData.get("rent_user_id").toString()

    var rentProductIndex : Int? = jsonData.get("rent_product_index").toString().toInt()
    var rentProductGroupIndex : Int? = jsonData.get("rent_product_group_index").toString().toInt()
    var rentProductGroupName : String? = jsonData.get("rent_product_group_name").toString()

    var rentProductName : String? = jsonData.get("rent_product_name").toString()
    var rentProductBarcode : Int? = jsonData.get("rent_product_barcode").toString().toInt()
    var rentStatus : Int? = jsonData.get("rent_status").toString().toInt()

    var rentTimeStart : String? = jsonData.get("rent_time_start").toString()
    var rentTimeEnd : String? = jsonData.get("rent_time_end").toString()
    var rentTimeReturn : String? = jsonData.get("rent_time_return").toString()
}
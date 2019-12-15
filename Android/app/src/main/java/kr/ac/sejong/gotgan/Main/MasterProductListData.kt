package kr.ac.sejong.gotgan.Main

import org.json.JSONArray
import org.json.JSONObject

class MasterProductListData (_productListJsonData : JSONObject, _productOverView : JSONObject) {

    var groupCountAvailable : Int? = _productOverView.get("group_count_available").toString().toInt()
    var groupCountRent : Int? = _productOverView.get("group_count_rent").toString().toInt()
    var groupCountUnavailable : Int? = _productOverView.get("group_count_unavailable").toString().toInt()
    var groupCountBroken : Int? = _productOverView.get("group_count_broken").toString().toInt()
    var groupCountRepair : Int? = _productOverView.get("group_count_repair").toString().toInt()

    var groupIndex : Int? = _productListJsonData.get("group_index").toString().toInt()
    var groupName : String? = _productListJsonData.get("group_name").toString()
    var groupRentAble : Int? = _productListJsonData.get("group_rentable").toString().toInt()
    var groupPriority : Int? = _productListJsonData.get("group_priority").toString().toInt()

    var products : JSONArray = JSONArray()
}
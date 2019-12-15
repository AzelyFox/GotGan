package kr.ac.sejong.gotgan.Main

import kr.ac.sejong.gotgan.BackEndAPI.*
import org.json.JSONObject

class MasterInOutManagementData (jsonObject: JSONObject) {
    var rentIndex : String? = jsonObject.get(rentIndexStr).toString()
    var rentUserIndex : String? = jsonObject.get(rentUserIndexStr).toString()
    var rentUserName : String? = jsonObject.get(rentUserNameStr).toString()
    var rentUserId : String? = jsonObject.get(rentUserIdStr).toString()
    var rentProductIndex : String? = jsonObject.get(rentProductIndexStr).toString()
    var rentProductGroupIndex : String? = jsonObject.get(rentProductGroupIndexStr).toString()
    var rentProductGroupName : String? = jsonObject.get(rentProductGroupNameStr).toString()
    var rentProductName : String? = jsonObject.get(rentProductNameStr).toString()
    var rentProductBarcode : String? = jsonObject.get(rentProductBarcodeStr).toString()
    var rentStatus : String? = jsonObject.get(rentStatusStr).toString()
    var rentTimeStart : String? = jsonObject.get(rentTimeStartStr).toString()
    var rentTimeEnd : String? = jsonObject.get(rentTimeEndStr).toString()
    var rentTimeReturn : String? = jsonObject.get(rentTimeReturnStr).toString()
}
package kr.ac.sejong.gotgan.Main

import kr.ac.sejong.gotgan.BackEndAPI.*
import org.json.JSONObject

class UserRentStatusData (rentJSONData : JSONObject) {
    var productName: String? = rentJSONData.get(rentProductNameStr).toString()
    var rentStatus: String? = null
    var rentStartDate: String? = rentJSONData.get(rentTimeStartStr).toString().split(" ")[0]
    var rentEndPlanDate: String? = rentJSONData.get(rentTimeEndStr).toString()
    var rentEndFinnishDate: String? = rentJSONData.get(rentTimeReturnStr).toString()

    init {
        when(rentJSONData.get(rentStatusStr).toString().toInt()) {
            0 -> rentStatus = "완료됨"
            1 -> rentStatus = "대여 신청됨"
            2 -> rentStatus = "대여중"
        }
        if (rentEndPlanDate != "null")
            rentEndPlanDate = rentEndPlanDate!!.split(" ")[0]
        else
            rentEndPlanDate = ""

        if (rentEndFinnishDate != "null")
            rentEndFinnishDate = rentEndFinnishDate!!.split(" ")[0]
        else
            rentEndFinnishDate = ""
    }
}
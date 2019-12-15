package kr.ac.sejong.gotgan.BackEndAPI.RentSystem

import kr.ac.sejong.gotgan.BackEndAPI.BackEnd_API
import kr.ac.sejong.gotgan.BackEndAPI.*

class BackEndAPI_RentSystemRentAdd(_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    fun httpsPostSend_RentAdd(_session: String, _rentProductIndexOrBarcode : String, _rentTimeStart : String, _mode: String): String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        when (_mode) {
            "productBarcode" -> data!!.append(productBarcodeStr).append(assignment).append(_rentProductIndexOrBarcode).append(and)
            "rentIndex" -> data!!.append(rentProductStr).append(assignment).append(_rentProductIndexOrBarcode).append(and)
        }
        data!!.append(rentTimeStartStr).append(assignment).append(_rentTimeStart)
        return super.httpPostDataSendAndReceiveData()
    }
}
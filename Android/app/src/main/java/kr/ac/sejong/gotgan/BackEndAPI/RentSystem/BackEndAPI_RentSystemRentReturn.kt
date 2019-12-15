package kr.ac.sejong.gotgan.BackEndAPI.RentSystem

import kr.ac.sejong.gotgan.BackEndAPI.BackEnd_API
import kr.ac.sejong.gotgan.BackEndAPI.*

class BackEndAPI_RentSystemRentReturn(_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    fun httpsPostSend_RentReturn(_session: String, _rentIndexOrBarcode : String, _mode: String): String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        when (_mode) {
            "productBarcode" -> data!!.append(productBarcodeStr).append(assignment).append(_rentIndexOrBarcode).append(and)
            "rentIndex" -> data!!.append(rentIndexStr).append(assignment).append(_rentIndexOrBarcode).append(and)
        }
        return super.httpPostDataSendAndReceiveData()
    }
}
package kr.ac.sejong.gotgan.BackEndAPI.RentSystem

import kr.ac.sejong.gotgan.BackEndAPI.*

class BackEndAPI_RentSystemRentList (_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    fun httpsPostSend_RentList(_session : String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session)
        return super.httpPostDataSendAndReceiveData()
    }
    
    fun httpsPostSend_RentList(_session : String, _index : String, _mode : String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        when (_mode) {
            "rentIndex" -> data!!.append(rentIndexStr).append(assignment).append(_index)
            "rentUser" -> data!!.append(rentUserStr).append(assignment).append(_index)
            "rentProduct" -> data!!.append(rentProductStr).append(assignment).append(_index)
            "productBarcode" -> data!!.append(productBarcodeStr).append(assignment).append(_index)
            "rentStatus" -> data!!.append(rentStatusStr).append(assignment).append(_index)
            "rentDelayed" -> data!!.append(rentDelayedStr).append(assignment).append(_index)
        }
        return super.httpPostDataSendAndReceiveData()
    }

}
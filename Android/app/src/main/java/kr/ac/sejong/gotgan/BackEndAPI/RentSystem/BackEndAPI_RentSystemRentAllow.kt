package kr.ac.sejong.gotgan.BackEndAPI.RentSystem

import kr.ac.sejong.gotgan.BackEndAPI.*

class BackEndAPI_RentSystemRentAllow (_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    fun httpsPostSend_RentAllow(_session : String, _productBarcodeOrRentIndex : Int, _mode : String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        when (_mode) {
            "productBarcode" -> data!!.append(productBarcodeStr).append(assignment).append(_productBarcodeOrRentIndex)
            "rentIndex" -> data!!.append(rentIndexStr).append(assignment).append(_productBarcodeOrRentIndex)
        }
        return super.httpPostDataSendAndReceiveData()
    }
/*
    fun httpsPostSend_RentAllow(_session : String, _productBarcode : Int, _rentIndex : Int) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        data!!.append(productBarcodeStr).append(assignment).append(_productBarcode).append(and)
        data!!.append(rentIndexStr).append(assignment).append(_rentIndex)
        return super.httpPostDataSendAndReceiveData()
    }
*/
}
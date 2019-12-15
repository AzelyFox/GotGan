package kr.ac.sejong.gotgan.BackEndAPI.ProductSystem

import kr.ac.sejong.gotgan.BackEndAPI.*

class BackEndAPI_ProductSystemProductList (_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    // assignment == "="

    fun httpsPostSend_ProductList(_session : String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session)
        return super.httpPostDataSendAndReceiveData()
    }

    fun httpsPostSend_ProductList(_session : String, _indexOrBarcodeOrGroup : String, _mode : String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        when (_mode) {
            "productIndex" -> data!!.append(productIndexStr).append(assignment).append(_indexOrBarcodeOrGroup)
            "productBarcode" -> data!!.append(productBarcodeStr).append(assignment).append(_indexOrBarcodeOrGroup)
            "productGroup" -> data!!.append(productGroupStr).append(assignment).append(_indexOrBarcodeOrGroup)
        }
        return super.httpPostDataSendAndReceiveData()
    }

    fun httpsPostSend_ProductList(_session : String, _indexOrBarcode : String, _group : Int, _mode : String) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(
            and
        )
        when (_mode) {
            "productIndex" -> data!!.append(productIndexStr).append(
                assignment
            ).append(_indexOrBarcode)
            "productBarcode" -> data!!.append(productBarcodeStr).append(
                assignment
            ).append(_indexOrBarcode)
        }
        data!!.append(productGroupStr).append(assignment).append(_group)
        return super.httpPostDataSendAndReceiveData()
    }

}
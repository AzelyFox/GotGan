package kr.ac.sejong.gotgan.BackEndAPI.ProductSystem

import kr.ac.sejong.gotgan.BackEndAPI.*
import org.json.JSONArray

class BackEndAPI_ProductSystemProductAdd (_apiName: String,_defaultUseCaches: Boolean, _doInput: Boolean, _doOutput: Boolean) : BackEnd_API() {

    private var apiName: String = _apiName

    init {
        httpsPostConnect(apiName)
        httpsPostSetting(_defaultUseCaches, _doInput, _doOutput)
    }

    // assignment == "="

    fun httpsPostSend_ProductAdd(_session : String, _products : JSONArray) : String? {
        data = StringBuffer()
        data!!.append(sessionStr).append(assignment).append(_session).append(and)
        data!!.append(productsStr).append(assignment).append(_products)
        return super.httpPostDataSendAndReceiveData()
    }
}
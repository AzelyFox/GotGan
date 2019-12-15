package kr.ac.sejong.gotgan.Main

import android.app.ProgressDialog
import android.content.Context
import android.os.AsyncTask
import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AlertDialog
import androidx.appcompat.widget.AppCompatTextView
import androidx.recyclerview.widget.RecyclerView
import com.google.android.material.button.MaterialButton
import kr.ac.sejong.gotgan.BackEndAPI.RentSystem.BackEndAPI_RentSystemRentAllow
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.inOutManagementRecyclerView
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.inOutManagementRecyclerViewInsteadTextView
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.rentListArrayList
import kr.ac.sejong.gotgan.R
import org.json.JSONObject

class MasterHomeRecyclerViewAdapter(_session : String, _context : Context, _homeInOutArrayList : ArrayList<MasterHomeInOutData>) : RecyclerView.Adapter<MasterHomeRecyclerViewAdapter.ViewHolder>() {

    private var homeInOutArrayList : ArrayList<MasterHomeInOutData>? = null
    private var progressDialog : ProgressDialog? = null
    private var context : Context? = null
    private var session : String? = null

    init {
        session = _session
        context = _context
        homeInOutArrayList = _homeInOutArrayList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        return ViewHolder(LayoutInflater.from(parent.context).inflate(R.layout.home_inout_list_item, parent, false))
    }

    override fun getItemCount(): Int {
        return homeInOutArrayList!!.size
    }

    private fun showProgressDialog(title : String, _context : Context) {
        progressDialog = ProgressDialog(_context)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL)
        progressDialog!!.setTitle(title)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.homeInOutUserNameAppCompatTextView.text = homeInOutArrayList!![position].rentUserName
        holder.homeInOutProductNameAppCompatTextView.text = homeInOutArrayList!![position].rentProductName
        holder.homeInOutAllowMaterialButton.setOnClickListener {
            // rent_allow
            val allowTask = RecyclerViewRentAllowOrDeleteManager(context!!)
            allowTask.execute(session, "0", "1", "1", "rentAllow", homeInOutArrayList!![position].rentIndex.toString(), homeInOutArrayList!![position].rentUserName, position.toString())
            showProgressDialog(context!!.resources.getString(R.string.processing), context!!)
        }
        holder.homeInOutDenyButton.setOnClickListener {
            // rent_delete
            val allowTask = RecyclerViewRentAllowOrDeleteManager(context!!)
            allowTask.execute(session, "0", "1", "1", "rentDeny", homeInOutArrayList!![position].rentIndex.toString(), homeInOutArrayList!![position].rentUserName, position.toString())
            showProgressDialog(context!!.resources.getString(R.string.processing), context!!)
        }
    }

    inner class ViewHolder (view : View) : RecyclerView.ViewHolder(view) {
        var homeInOutUserNameAppCompatTextView : AppCompatTextView = view.findViewById(R.id.home_inout_list_item_user_name)
        var homeInOutProductNameAppCompatTextView : AppCompatTextView = view.findViewById(R.id.home_inout_list_item_product_name)
        var homeInOutAllowMaterialButton : MaterialButton = view.findViewById(R.id.home_inout_list_item_allow_button)
        var homeInOutDenyButton : Button = view.findViewById(R.id.home_inout_list_item_deny_button)
    }

    private inner class RecyclerViewRentAllowOrDeleteManager(_context : Context) : HttpPostSendRentAllowOrRentDelete() {
        // string to json

        val context = _context

        fun getRentAllResultJSONData(): JSONObject { return JSONObject(allResultData) }
        fun getRentAllResultData(): String? { return allResultData  }
        fun getUserName() : String? { return rentUserName }
        fun getRentModeName() : String? { return modeName }
        fun getALIndex() : Int? { return arrayListIndex }

        override fun onPreTask() {}

        override fun onUpdate(prgInt: Int) {
            progressDialog!!.progress = prgInt
        }

        override fun onFinish(result: Int) {
            if (progressDialog != null)
                progressDialog!!.dismiss()

            Log.d("Rent AorD Result", getRentAllResultData())

            if (result == 1 && getRentAllResultJSONData()["result"] == 0) {
                // 성공시 처리
                if (getRentModeName() == "rentAllow")
                    Toast.makeText(context, getUserName() + " 님의 " + context.resources.getString(R.string.allow_success_message), Toast.LENGTH_LONG).show()
                else if (getRentModeName() == "rentDeny")
                    Toast.makeText(context, getUserName() + " 님의 " + context.resources.getString(R.string.allow_deny_message), Toast.LENGTH_LONG).show()

                rentListArrayList.removeAt(getALIndex()!!)

                if (rentListArrayList.isEmpty()) {
                    inOutManagementRecyclerView!!.visibility = View.GONE
                    inOutManagementRecyclerViewInsteadTextView!!.visibility = View.VISIBLE
                }
                else {
                    inOutManagementRecyclerView!!.visibility = View.VISIBLE
                    inOutManagementRecyclerViewInsteadTextView!!.visibility = View.GONE
                    //inOutManagementRecyclerView!!.adapter = inOutManagementRecyclerView!!.adapter
                }
                inOutManagementRecyclerView!!.adapter = inOutManagementRecyclerView!!.adapter

            }
            else {
                val builder = AlertDialog.Builder(context, R.style.AppCompatErrorAlertDialogStyle)
                builder.setTitle(R.string.loading_data_fail)
                when (getRentAllResultJSONData()["result"]) {
                    -1 -> builder.setMessage(R.string.implementation_error)
                    -2 -> builder.setMessage(R.string.server_error)
                    -3 -> builder.setMessage(R.string.dont_have_permission_error)
                    -4 -> builder.setMessage(R.string.searching_error)
                }
                //if ("session") // session 로그인
                //    builder.setMessage(R.string.expire_session_error)
                builder.setPositiveButton(android.R.string.ok, null)
                builder.show()
            }
            return
        }
    }

    abstract class HttpPostSendRentAllowOrRentDelete: AsyncTask<String, Int, Int>() {

        private val rentAllowApiName = "rent_allow.php"
        private val rentDeleteApiName = "rent_delete.php"

        protected var allResultData: String? = null

        protected var modeName : String? = null
        protected var rentUserName : String? = null

        protected var arrayListIndex : Int? = null

        abstract fun onPreTask()
        abstract fun onUpdate(prgInt: Int)
        abstract fun onFinish(result: Int)

        override fun onPreExecute() {
            //super.onPreExecute()
            onPreTask()
        }

        override fun doInBackground(vararg params: String): Int {
            // params[0] == session
            // params[1] == defaultUseCaches
            // params[2] == doInput
            // params[3] == doOutput
            // params[4] == mode
            // params[5] == rentIndex
            // params[6] == rentUserName
            // params[7] == arrayListIndex

            var defaultUseCaches = false
            var doInput = false
            var doOutput = false
            if (params[1] == "1") defaultUseCaches = true
            if (params[2] == "1") doInput = true
            if (params[3] == "1") doOutput = true
            modeName = params[4]
            rentUserName = params[6]
            arrayListIndex = params[7].toInt()

            try {
                if (modeName == "rentAllow") {
                    val rentAllowAPI = BackEndAPI_RentSystemRentAllow(rentAllowApiName, defaultUseCaches, doInput, doOutput)
                    publishProgress(50)
                    allResultData = rentAllowAPI.httpsPostSend_RentAllow(params[0], params[5].toInt(), "rentIndex")
                }
                else if (modeName == "rentDeny") {
                    val rentAllowAPI = BackEndAPI_RentSystemRentAllow(rentDeleteApiName, defaultUseCaches, doInput, doOutput)
                    publishProgress(50)
                    allResultData = rentAllowAPI.httpsPostSend_RentAllow(params[0], params[5].toInt(), "rentIndex")
                }
                publishProgress(100)
            }
            catch (e: Exception) {
                Log.e("M-H-RecyclerView Error", "Message : " + e.message)
                Log.e("M-H-RecyclerView Error", "LocalizedMessage : " + e.localizedMessage)
                return 0
            }

            return 1
        }

        override fun onProgressUpdate(vararg values: Int?) {
            //super.onProgressUpdate(*values)
            onUpdate(values[0]!!)
        }

        override fun onPostExecute(result: Int?) {
            super.onPostExecute(result)
            onFinish(result!!)
        }
    }

}

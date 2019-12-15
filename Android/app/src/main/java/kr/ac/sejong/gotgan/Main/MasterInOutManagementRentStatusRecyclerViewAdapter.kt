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
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.rentStatusArrayList
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.rentStatusRecyclerView
import kr.ac.sejong.gotgan.Main.MainActivity_Master.Companion.rentStatusRecyclerViewInsteadTextView
import kr.ac.sejong.gotgan.R
import org.json.JSONObject

class MasterInOutManagementRentStatusRecyclerViewAdapter(_session : String, _context : Context, _InoutManagementArrayList : ArrayList<MasterInOutManagementData>) : RecyclerView.Adapter<MasterInOutManagementRentStatusRecyclerViewAdapter.ViewHolder>() {

    private var InoutManagementArrayList : ArrayList<MasterInOutManagementData>? = null
    private var progressDialog : ProgressDialog? = null
    private var context : Context? = null
    private var session : String? = null

    init {
        session = _session
        context = _context
        InoutManagementArrayList = _InoutManagementArrayList
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        return ViewHolder(LayoutInflater.from(parent.context).inflate(R.layout.inout_management_rent_status_item, parent, false))
    }

    override fun getItemCount(): Int {
        return InoutManagementArrayList!!.size
    }

    private fun showProgressDialog(title : String, _context : Context) {
        progressDialog = ProgressDialog(_context)
        progressDialog!!.setProgressStyle(ProgressDialog.STYLE_HORIZONTAL)
        progressDialog!!.setTitle(title)
        progressDialog!!.setCancelable(false)
        progressDialog!!.show()
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.inoutManagementRentStatusNameTextView.text = InoutManagementArrayList!![position].rentUserName
        holder.inoutManagementRentStatusProductNameTextView.text = InoutManagementArrayList!![position].rentProductName
        holder.inoutManagementRentStatusStartDateTextView.text = InoutManagementArrayList!![position].rentTimeStart
        holder.inoutManagementRentStatusAllowButton.setOnClickListener {
            // rent_allow
            showProgressDialog(context!!.resources.getString(R.string.processing), context!!)
            val allowTask = RecyclerViewRentAllowOrDeleteManager(context!!)
            allowTask.execute(session, "0", "1", "1", "rentAllow", InoutManagementArrayList!![position].rentIndex.toString(), InoutManagementArrayList!![position].rentUserName, position.toString())
        }
        holder.inoutManagementRentStatusDenyButton.setOnClickListener {
            // rent_delete
            showProgressDialog(context!!.resources.getString(R.string.processing), context!!)
            val allowTask = RecyclerViewRentAllowOrDeleteManager(context!!)
            allowTask.execute(session, "0", "1", "1", "rentDeny", InoutManagementArrayList!![position].rentIndex.toString(), InoutManagementArrayList!![position].rentUserName, position.toString())
        }
    }

    inner class ViewHolder (view : View) : RecyclerView.ViewHolder(view) {
        var inoutManagementRentStatusNameTextView : AppCompatTextView = view.findViewById(R.id.inout_management_rent_status_name_textView)
        var inoutManagementRentStatusProductNameTextView : AppCompatTextView = view.findViewById(R.id.inout_management_rent_status_product_name_textView)
        var inoutManagementRentStatusStartDateTextView : AppCompatTextView = view.findViewById(R.id.inout_management_rent_status_start_date_textView)
        var inoutManagementRentStatusAllowButton : MaterialButton = view.findViewById(R.id.inout_management_rent_status_allow_button)
        var inoutManagementRentStatusDenyButton : Button = view.findViewById(R.id.inout_management_rent_status_deny_button)
    }

    // adapter 부분 수정할 것
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

                //
                rentStatusArrayList.removeAt(getALIndex()!!)
                if (rentStatusArrayList.isEmpty()) {
                    rentStatusRecyclerView!!.visibility = View.GONE
                    rentStatusRecyclerViewInsteadTextView!!.visibility = View.VISIBLE
                }
                else {
                    rentStatusRecyclerView!!.visibility = View.VISIBLE
                    rentStatusRecyclerViewInsteadTextView!!.visibility = View.GONE
                    //rentStatusRecyclerView!!.adapter = rentStatusRecyclerView!!.adapter
                }
                rentStatusRecyclerView!!.adapter = rentStatusRecyclerView!!.adapter

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
                Log.e("InOutRent Error", "Message : " + e.message)
                Log.e("InOutRent Error", "LocalizedMessage : " + e.localizedMessage)
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

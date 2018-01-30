package com.mx.bluetooth.custom;

import java.util.ArrayList;

import com.mx.bluetooth.R;
import com.mx.bluetooth.adapter.Score_PreView_Adapter;
import com.mx.bluetooth.bean.GradePointBean_Net;

import android.app.Dialog;
import android.content.Context;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;

public class ScorePreView_Dialog {
	Context context;
	Dialogcallback dialogcallback;
	Dialog dialog;
	ArrayList<GradePointBean_Net> mGradePointBean_Local;
	Dialogcallback mDialogcallback;

	/**
	 * init the dialog
	 * 
	 * @return
	 */
	public ScorePreView_Dialog(Context con, ArrayList<GradePointBean_Net> mGradePointBean_Local, String TextScore_s,
			Dialogcallback dialogcallback) {
		this.context = con;
		this.mDialogcallback = dialogcallback;
		this.mGradePointBean_Local = mGradePointBean_Local;
		this.dialog = new Dialog(context, R.style.dialog);
		dialog.setCanceledOnTouchOutside(false);
		dialog.setCancelable(false);
		dialog.setContentView(R.layout.score_preview_dialog);
		ListView score_pre_listView = (ListView) dialog.findViewById(R.id.score_pre_listView);
		score_pre_listView.setOnItemClickListener(null);
		Score_PreView_Adapter Score_PreView_Adapter = new Score_PreView_Adapter(context, mGradePointBean_Local);
		score_pre_listView.setAdapter(Score_PreView_Adapter);
		ImageView close_window = (ImageView) dialog.findViewById(R.id.close_window);
		close_window.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				dismiss();
			}
		});
		TextView text_score = (TextView) dialog.findViewById(R.id.text_score);
		text_score.setText(TextScore_s);
		Button btn_commit = (Button) dialog.findViewById(R.id.btn_commit);
		Button btn_check = (Button) dialog.findViewById(R.id.btn_check);
		btn_commit.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				mDialogcallback.dialogdoupload("");
				dismiss();
			}
		});
		btn_check.setOnClickListener(new View.OnClickListener() {

			@Override
			public void onClick(View v) {
				// TODO Auto-generated method stub
				mDialogcallback.dialogdoreturn("");
				dismiss();
			}
		});

	}

	/**
	 * 设定一个interfack接口,使mydialog可以處理activity定義的事情
	 * 
	 * @author sfshine
	 * 
	 */
	public void show() {
		dialog.show();

	}

	public void hide() {
		dialog.hide();
	}

	public void dismiss() {
		dialog.dismiss();
	}

	public interface Dialogcallback {
		public void dialogdoupload(String string);

		public void dialogdoreturn(String string);
	}

	public void setDialogCallback(Dialogcallback dialogcallback) {
		this.dialogcallback = dialogcallback;
	}

	/**
	 * @category Set The Content of the TextView
	 */
	public void setData(ArrayList<GradePointBean_Net> mGradePointBean_Local) {
		this.mGradePointBean_Local = mGradePointBean_Local;
	}

}
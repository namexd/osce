package com.mx.bluetooth.adapter;

import java.util.ArrayList;
import java.util.List;

import com.mx.bluetooth.R;
import com.mx.bluetooth.audio.MediaManage;
import com.mx.bluetooth.bean.DataBean;
import com.mx.bluetooth.bean.GradePointBean_Net;
import com.mx.bluetooth.save.FileUtils;
import com.mx.bluetooth.util.Utils;

import android.content.Context;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class PopAdapter extends BaseAdapter {
	private ArrayList<DataBean> dataList;
	private Context context;
	private ArrayList<GradePointBean_Net> gradeListData;

	public List<DataBean> getDataList() {
		return dataList;
	}

	public void recoverData(ArrayList<DataBean> dataList) {
		if (dataList != null) {
			if (this.dataList == null) {
				this.dataList = new ArrayList<DataBean>();
			}
			this.dataList.addAll(dataList);
			PopAdapter.this.notifyDataSetChanged();
		}
	}

	public PopAdapter(Context context, ArrayList<DataBean> datalist, ArrayList<GradePointBean_Net> mAllPoints) {
		this.context = context;
		this.dataList = datalist;
		// dataList.addAll(datalist);
	}

	@Override
	public int getItemViewType(int position) {
		// if (position == 0) {
		// return 3;
		// } else {
		return dataList.get(position).getmTag();
		// }
	}

	@Override
	public int getViewTypeCount() {

		return super.getViewTypeCount() + 3;
	}

	@Override
	public int getCount() {
		return dataList.size();
	}

	@Override
	public Object getItem(int position) {
		return dataList.get(position);
	}

	@Override
	public long getItemId(int position) {
		return position;
	}

	@Override
	public View getView(final int position, View convertView, ViewGroup parent) {
		int itemViewType = getItemViewType(position);
		ViewHolderMic micHolder = null;
		ViewHolderMark markHolder = null;
		ViewHolderCamera cameraHolder = null;

		if (convertView == null) {

			switch (itemViewType) {

			case DataBean.AUDIO:
				convertView = LayoutInflater.from(parent.getContext()).inflate(R.layout.popupwindow_item_mic, null);
				micHolder = new ViewHolderMic();
				micHolder.imageDelete_mic = (ImageView) convertView.findViewById(R.id.image_close);
				micHolder.textTime_mic = (TextView) convertView.findViewById(R.id.text_messsage);
				micHolder.textPlay_mic = (TextView) convertView.findViewById(R.id.text_recard);
				convertView.setTag(micHolder);
				break;
			case DataBean.CAMERA:
				convertView = LayoutInflater.from(parent.getContext()).inflate(R.layout.popupwindow_item_camera, null);
				cameraHolder = new ViewHolderCamera();
				cameraHolder.imageCamera_camera = (ImageView) convertView.findViewById(R.id.image_camera);
				cameraHolder.imageDelete_camera = (ImageView) convertView.findViewById(R.id.image_delete);
				convertView.setTag(cameraHolder);
				break;
			case DataBean.TIMEPOINT:
				convertView = LayoutInflater.from(parent.getContext()).inflate(R.layout.popupwindow_item_mark, null);
				markHolder = new ViewHolderMark();
				markHolder.imageDelete_mark = (ImageView) convertView.findViewById(R.id.image_close);
				markHolder.textTime_mark = (TextView) convertView.findViewById(R.id.text_messsage);
				convertView.setTag(markHolder);
				break;
			}

		} else {

			switch (itemViewType) {

			case DataBean.AUDIO:
				micHolder = (ViewHolderMic) convertView.getTag();

				break;
			case DataBean.CAMERA:
				cameraHolder = (ViewHolderCamera) convertView.getTag();
				break;
			case DataBean.TIMEPOINT:
				markHolder = (ViewHolderMark) convertView.getTag();

				break;
			}
		}

		switch (itemViewType) {

		case DataBean.AUDIO:
			final String saveAudioPath = dataList.get(position).getFilePath();
			micHolder.textTime_mic.setText(Math.round(dataList.get(position).getTime()) + "s");
			micHolder.textPlay_mic.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					// 播放音频
					MediaManage.playSound(dataList.get(position).getFilePath(), null);
					Log.i("*******", "Down");
				}
			});

			micHolder.imageDelete_mic.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					dataList.remove(position);
					FileUtils.deleteFile(saveAudioPath);
					PopAdapter.this.notifyDataSetChanged();
				}
			});

			break;
		case DataBean.CAMERA:
			final String savePath = dataList.get(position).getFilePath();
			cameraHolder.imageCamera_camera.setImageBitmap(BitmapFactory.decodeFile(savePath));
			cameraHolder.imageDelete_camera.setOnClickListener(new OnClickListener() {
				@Override
				public void onClick(View v) {
					dataList.remove(position);
					PopAdapter.this.notifyDataSetChanged();

					new AsyncTask<Void, Void, Void>() {

						@Override
						protected Void doInBackground(Void... params) {
							FileUtils.deleteFile(savePath);
							return null;
						}
					}.execute();

				}
			});

			break;
		case DataBean.TIMEPOINT:

			markHolder.textTime_mark
					.setText(Utils.long2SimpleData("yyyy-MM-dd HH:mm:ss", dataList.get(position).getTimePoint()));
			markHolder.imageDelete_mark.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					dataList.remove(position);
					PopAdapter.this.notifyDataSetChanged();
				}
			});

			break;
		}

		return convertView;
	}

	// 录音
	static class ViewHolderMic {
		ImageView imageDelete_mic;
		TextView textTime_mic;
		TextView textPlay_mic;
	}

	// 描点
	static class ViewHolderMark {
		ImageView imageDelete_mark;
		TextView textTime_mark;
	}

	// 照相
	static class ViewHolderCamera {
		ImageView imageCamera_camera;
		ImageView imageDelete_camera;
	}

}

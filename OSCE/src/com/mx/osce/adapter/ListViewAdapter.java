package com.mx.osce.adapter;

import java.util.ArrayList;

import com.mx.osce.R;
import com.mx.osce.bean.GradePointBean;
import com.mx.osce.custom.CustomSeekBarNotUse;
import com.mx.osce.custom.CustomSeekBarNotUse.OnMySeekBarChangeListener;
import com.mx.osce.custom.SeekView_score;

import android.content.Context;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.SeekBar;
import android.widget.SeekBar.OnSeekBarChangeListener;
import android.widget.TextView;

public class ListViewAdapter extends BaseAdapter {

	public static final String TAG = "ListViewAdapter";
	/** 使用上下文 */
	private Context context;
	/** listview只是展示单个的考核项 */
	private ArrayList<GradePointBean> allPoints;

	private int pointIndex = 0;

	private boolean showPoint = false;

	/**
	 * 是否显示考点
	 * 
	 * @param isShow
	 *            是否要显示评分标准
	 * @return 返回true,显示；返回false,隐藏
	 */
	public void isShowItemPoint() {
		showPoint = true;
		this.notifyDataSetChanged();
	}

	public void isHideItemPoint() {
		showPoint = false;
		this.notifyDataSetChanged();
	}

	public ListViewAdapter(Context context, ArrayList<GradePointBean> allPoints) {
		this.context = context;
		this.allPoints = new ArrayList<GradePointBean>();
		if (allPoints != null) {
			this.allPoints.addAll(allPoints);
		}
	}

	public void setGradePointBeanIndex(int index) {
		this.pointIndex = index;
	}

	public int getCount() {
		return allPoints.get(pointIndex).getTest_term().size();
	}

	public Object getItem(int position) {
		return allPoints.get(pointIndex).getTest_term().get(position);
	}

	public long getItemId(int position) {
		return 0;
	}

	public View getView(final int position, View convertView, ViewGroup parent) {
		ViewHolder holder = null;
		if (convertView == null) {
			convertView = LayoutInflater.from(context).inflate(R.layout.listview_item, null);

			holder = new ViewHolder();

			holder.item_number = (TextView) convertView.findViewById(R.id.tv_fragment_test_detail_point_item_num);
			holder.item_content = (TextView) convertView.findViewById(R.id.tv_fragment_test_detail_point_item_content);
			holder.item_ponit = (TextView) convertView.findViewById(R.id.tv_fragment_test_detaile_point_item_point);
			holder.pointBar = (SeekView_score) convertView.findViewById(R.id.seekBar_tg2);

			convertView.setTag(holder);
		} else {

			holder = (ViewHolder) convertView.getTag();
		}

		holder.item_number.setText("考核项：" + allPoints.get(pointIndex).getTest_term().get(position).getSort());

		holder.item_content.setText(allPoints.get(pointIndex).getTest_term().get(position).getContent());
		
		if (showPoint) {//显示/隐藏 考点
			holder.item_ponit.setText(allPoints.get(pointIndex).getTest_term().get(position).getAnswer());
			holder.item_ponit.setVisibility(View.VISIBLE);
		} else {
			holder.item_ponit.setVisibility(View.GONE);
		}

		holder.pointBar.setMAX(Integer.parseInt(allPoints.get(pointIndex).getTest_term().get(position).getScore()));


		holder.pointBar.setProgressBypositon(Integer.parseInt(allPoints.get(pointIndex).getTest_term().get(position).getReal()));
		holder.pointBar.setOnSeekBarChangeListener(new SeekView_score.OnSeekBarChangeListener() {
            @Override
            public void onProgressBefore() {
               
            }

            @Override
            public void onProgressAfter(int i) {
               
            allPoints.get(pointIndex).getTest_term().get(position).setReal("" + i);
             System.out.println("保存"+i);
            }

			@Override
			public void onProgressChanged(SeekView_score seekView, double progressLow) {
				// TODO Auto-generated method stub
				
			}
         });

		return convertView;
	}

	class ViewHolder {

		public TextView item_number;
		public TextView item_content;
		private TextView item_ponit;
		public SeekView_score pointBar;

	}

}

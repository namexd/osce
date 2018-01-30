package com.mx.test.custom;




import com.mx.test.R;

import android.app.Dialog;
import android.content.Context;

public class LoadingDialog extends Dialog {
      
	public LoadingDialog(Context context) {
		super(context,R.style.MyDialogStyle);
		setContentView(getLayoutInflater().inflate(R.layout.dialog, null));
		
	}

}

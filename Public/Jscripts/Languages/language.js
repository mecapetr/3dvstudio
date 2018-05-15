function translate(text){
	if(!isEmtpy(dict[text])){
		return dict[text].toString();
	}else return text;
}
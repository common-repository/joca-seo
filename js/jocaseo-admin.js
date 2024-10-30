function joca_showInput(input, toShow)
{
	input.style.display = 'none';
	document.getElementsByClassName(toShow)[0].style.display = "block";
	document.getElementsByClassName(toShow)[0].focus();
	document.getElementsByClassName(toShow)[0].select();
}

function joca_disableInput(input, toShow)
{
	input.style.display = 'none';
	var value = input.value;
	value = value.replace(/\</g,"&lt;")   //for <
	value = value.replace(/\>/g,"&gt;")   //for >
	input.value = value;
	document.getElementsByClassName(toShow)[0].style.display = "block";
	if (input.value != '')
		document.getElementsByClassName(toShow)[0].innerHTML = input.value;
	else
		document.getElementsByClassName(toShow)[0].innerHTML = input.placeholder;
}

function joca_checkLen(e, textareaField)
{
	if (e.keyCode == 13 || e.keyCode == 27)
	{
		e.preventDefault();
		if (typeof textareaField.onblur == "function") {
			textareaField.onblur.apply(textareaField);
		}
		return false;
	}
	var len = 160;
	if(textareaField.value.length > len) {
        textareaField.value = textareaField.value.substr(0, len);
        return false;
    }
}

function joca_checkEnter(e, input)
{
	if (e.keyCode == 13 || e.keyCode == 27)
	{
		e.preventDefault();
		if (typeof input.onblur == "function") {
			input.onblur.apply(input);
		}
		return false;
	}
}

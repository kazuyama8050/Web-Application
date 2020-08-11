"use strict";
{
    function changeDisplay(change_selection,change_form){
        const formItem=document.querySelectorAll(change_selection);
        const form_content=document.querySelectorAll(change_form);

        formItem.forEach(form=>{
            form.addEventListener("click",e=>{
                e.preventDefault();

                formItem.forEach(form=>{
                    form.classList.remove("active");
                });
                form.classList.add("active");

                form_content.forEach(content=>{
                    content.classList.remove("active");
                });
                document.getElementById(form.dataset.id).classList.add("active");
            });
        });
    }
    changeDisplay("#selection ol a",".form_format");
    changeDisplay("#selection_sub ol a",".form_format_sub");
}

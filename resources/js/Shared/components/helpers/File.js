export const convertDataToFile = async dataUrl => {
    return new Promise(async (accept, reject) => {
        const type = dataUrl.match(/^data:(.+);base64/)?.[1];
        const res = await fetch(dataUrl);
        const blob = await res.blob();
        const file = new File([blob], new Date().toISOString().replace(/[-:.]/g, ""), {type});
        accept(file);
    });
}

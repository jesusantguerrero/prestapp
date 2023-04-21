export const formatMoney = (value: string|number, symbol = "DOP", settings = {}) => {
    try {
        const formattedValue =  new Intl.NumberFormat("en-US", {
          style: "currency",
          currency: symbol,
          currencyDisplay: "symbol"
        }).format(Number(value) || 0);

        if (settings.hideSymbol) {
          return formattedValue.replace(symbol, '');
        }
        return formattedValue;
    } catch (err) {
        return value;
    }
}

export default formatMoney;

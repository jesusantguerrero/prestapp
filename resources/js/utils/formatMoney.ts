export const formatMoney = (value: string, symbol = "DOP") => {
    try {
        return new Intl.NumberFormat("en-US", {
          style: "currency",
          currency: symbol,
          currencyDisplay: "symbol"
        }).format(Number(value) || 0);
    } catch (err) {
        return value;
    }
}

export default formatMoney;

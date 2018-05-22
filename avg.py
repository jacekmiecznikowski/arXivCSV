import csv
import statistics


def main():
    with open('all.csv', newline='') as file:
        reader = csv.DictReader(file)
        column = {key: [] for key in reader.fieldnames}
        for row in reader:
            for key in reader.fieldnames:
                column[key].append(row[key])
    print('h-index Average =', statistics.mean(map(float, column['h-index'])))
    print('m quotient Average =', statistics.mean(map(float, column['m quotient'])))
    print('e-index Average =', statistics.mean(map(float, column['e-index'])))
    print('m-index Average =', statistics.mean(map(float, column['m-index'])))
    print('r-index Average =', statistics.mean(map(float, column['r-index'])))
    print('ar-index Average =', statistics.mean(map(float, column['ar-index'])))
    print('h-index Median =', statistics.median(map(float, column['h-index'])))
    print('m quotient Median =', statistics.median(map(float, column['m quotient'])))
    print('e-index Median =', statistics.median(map(float, column['e-index'])))
    print('m-index Median =', statistics.median(map(float, column['m-index'])))
    print('r-index Median =', statistics.median(map(float, column['r-index'])))
    print('ar-index Median =', statistics.median(map(float, column['ar-index'])))
    print('h-index Mode =', statistics.mode(map(float, column['h-index'])))
    print('m quotient Mode =', statistics.mode(map(float, column['m quotient'])))
    print('e-index Mode =', statistics.mode(map(float, column['e-index'])))
    print('m-index Mode =', statistics.mode(map(float, column['m-index'])))
    print('r-index Mode =', statistics.mode(map(float, column['r-index'])))
    print('ar-index Mode =', statistics.mode(map(float, column['ar-index'])))
    print('h-index Min =', min(map(float, column['h-index'])))
    print('m quotient Min =', min(map(float, column['m quotient'])))
    print('e-index Min =', min(map(float, column['e-index'])))
    print('m-index Min =', min(map(float, column['m-index'])))
    print('r-index Min =', min(map(float, column['r-index'])))
    print('ar-index Min =', min(map(float, column['ar-index'])))
    print('h-index Max =', max(map(float, column['h-index'])))
    print('m quotient Max =', max(map(float, column['m quotient'])))
    print('e-index Max =', max(map(float, column['e-index'])))
    print('m-index Max =', max(map(float, column['m-index'])))
    print('r-index Max =', max(map(float, column['r-index'])))
    print('ar-index Max =', max(map(float, column['ar-index'])))


if __name__ == '__main__':
    main()
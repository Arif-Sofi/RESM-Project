<style>
    .calendar-container {
        width: 100%;
        box-sizing: border-box;
        max-width: 1200px;
        margin: 0 auto;
    }

    #calendar {
        width: 100%;
        height: calc(100vh - 250px);
        min-height: 500px;
    }

    .fc {
        width: 100% !important;
        height: 100% !important;
    }

    @media (max-width: 768px) {
        #calendar {
            height: calc(100vh - 300px);
        }
        .calendar-container {
             max-width: 100%;
             margin: 0;
        }
    }

    @media (min-width: 769px) {
        .calendar-container {
            max-width: 1200px;
            margin: 0 auto;
        }
    }

    #calendar a {
        color: inherit;
        text-decoration: none;
    }

    nav a {
        text-decoration: none !important;
    }
</style>

class OpTabs {
  constructor(selector) {
    this.selector = selector;
    this.initialize()
  }

  selector
  wrappers
  classes = {
    wrapper: "op-tabs-wrapper",
    contentWrapper: "op-content",
    contentItem: "op-content-item",
    tabsWrapper: "op-tabs",
    tabsItem: "op-tabs-item",
    dataTab: "tabs-tab",
    activeClass: "active",
    initializedContentItemsClass: "op-content-item_initialized"
  }

  initialize() {
    this.wrappers = jQuery(this.selector)
    if (!this.wrappers.length) return
    this.initTabs()
  }

  initTabs() {
    this.wrappers.each((i, wrapper) => {
      const contentItems = jQuery(wrapper).find("." + this.classes.contentItem)
      const tabsContainer = jQuery(wrapper).find("." + this.classes.tabsWrapper)
      const tabItems = tabsContainer.find("." + this.classes.tabsItem)
      jQuery(contentItems).addClass(this.classes.initializedContentItemsClass)
      jQuery(contentItems[0]).addClass(this.classes.activeClass)
      //jQuery(tabItems[0]).addClass(this.classes.activeClass)
      if (window.innerWidth > 767) {
        tabItems.css("width", (100 / jQuery(".op-tabs-item").length) + "%")
      }
      tabItems.on("click", ({ currentTarget }) => {
        const tab = currentTarget
        const tabTargetNumber = jQuery(tab).data(this.classes.dataTab)
        const contentItemTarget = jQuery(wrapper).find(`[data-tabs-target="jQuery{tabTargetNumber}"]`)
        tabItems.removeClass(this.classes.activeClass)
        jQuery(tab).addClass(this.classes.activeClass)
        if (!contentItemTarget.length) return
        contentItems.removeClass(this.classes.activeClass)
        contentItemTarget.addClass(this.classes.activeClass)
      })
    })
  }
}
